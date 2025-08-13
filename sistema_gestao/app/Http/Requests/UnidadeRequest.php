<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnidadeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unidadeId = $this->route('unidade') ? $this->route('unidade')->id : null;
        
        return [
            'nome_fantasia' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => [
                'required',
                'string',
                'size:14',
                'regex:/^\d{14}$/',
                Rule::unique('unidades', 'cnpj')->ignore($unidadeId)
            ],
            'bandeira_id' => 'required|exists:bandeiras,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome_fantasia.required' => 'O campo nome fantasia é obrigatório.',
            'nome_fantasia.max' => 'O nome fantasia não pode ter mais de 255 caracteres.',
            'razao_social.required' => 'O campo razão social é obrigatório.',
            'razao_social.max' => 'A razão social não pode ter mais de 255 caracteres.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.size' => 'O CNPJ deve ter 14 dígitos.',
            'cnpj.regex' => 'O CNPJ deve conter apenas números.',
            'cnpj.unique' => 'Já existe uma unidade cadastrada com este CNPJ.',
            'bandeira_id.required' => 'A bandeira é obrigatória.',
            'bandeira_id.exists' => 'A bandeira selecionada é inválida.',
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Remove caracteres não numéricos do CNPJ
        if ($this->has('cnpj')) {
            $this->merge([
                'cnpj' => preg_replace('/[^0-9]/', '', $this->cnpj)
            ]);
        }
    }
}
