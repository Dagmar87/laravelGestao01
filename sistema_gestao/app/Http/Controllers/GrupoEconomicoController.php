<?php

namespace App\Http\Controllers;

use App\Models\GrupoEconomico;
use App\Http\Requests\GrupoEconomicoRequest;
use Illuminate\Http\Request;

class GrupoEconomicoController extends Controller
{
    /**
     * Exibe uma listagem dos recursos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gruposEconomicos = GrupoEconomico::orderBy('nome')->paginate(10);
        return view('grupo_economicos.index', compact('gruposEconomicos'));
    }

    /**
     * Mostra o formulário para criar um novo recurso.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('grupo_economicos.create');
    }

    /**
     * Armazena um recurso recém-criado no armazenamento.
     *
     * @param  \App\Http\Requests\GrupoEconomicoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GrupoEconomicoRequest $request)
    {
        try {
            $grupoEconomico = GrupoEconomico::create($request->validated());
            return redirect()
                ->route('grupo_economicos.index')
                ->with('success', 'Grupo econômico criado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar grupo econômico: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return \Illuminate\Http\Response
     */
    public function show(GrupoEconomico $grupoEconomico)
    {
        return view('grupo_economicos.show', compact('grupoEconomico'));
    }

    /**
     * Mostra o formulário para editar o recurso especificado.
     *
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return \Illuminate\Http\Response
     */
    public function edit(GrupoEconomico $grupoEconomico)
    {
        return view('grupo_economicos.edit', compact('grupoEconomico'));
    }

    /**
     * Atualiza o recurso especificado no armazenamento.
     *
     * @param  \App\Http\Requests\GrupoEconomicoRequest  $request
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GrupoEconomicoRequest $request, GrupoEconomico $grupoEconomico)
    {
        try {
            $grupoEconomico->update($request->validated());
            return redirect()
                ->route('grupo_economicos.index')
                ->with('success', 'Grupo econômico atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar grupo econômico: ' . $e->getMessage());
        }
    }

    /**
     * Remove o recurso especificado do armazenamento.
     *
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(GrupoEconomico $grupoEconomico)
    {
        try {
            if ($grupoEconomico->bandeiras()->exists()) {
                return redirect()
                    ->route('grupo_economicos.index')
                    ->with('error', 'Não é possível excluir o grupo econômico pois existem bandeiras vinculadas a ele.');
            }
            
            $grupoEconomico->delete();
            return redirect()
                ->route('grupo_economicos.index')
                ->with('success', 'Grupo econômico excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('grupo_economicos.index')
                ->with('error', 'Erro ao excluir grupo econômico: ' . $e->getMessage());
        }
    }
}