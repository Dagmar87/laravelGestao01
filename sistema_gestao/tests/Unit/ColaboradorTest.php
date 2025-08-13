<?php

namespace Tests\Unit;

use App\Models\Colaborador;
use App\Models\Unidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um colaborador.
     *
     * @return void
     */
    public function test_criar_colaborador()
    {
        // Criar uma unidade para associar ao colaborador
        $unidade = Unidade::factory()->create();

        // Criar um colaborador
        $colaborador = Colaborador::create([
            'nome' => 'João da Silva',
            'cpf' => '12345678909',
            'email' => 'joao@example.com',
            'unidade_id' => $unidade->id,
        ]);

        // Verificar se o colaborador foi criado corretamente
        $this->assertInstanceOf(Colaborador::class, $colaborador);
        $this->assertEquals('João da Silva', $colaborador->nome);
        $this->assertEquals('12345678909', $colaborador->cpf);
        $this->assertEquals('joao@example.com', $colaborador->email);
        $this->assertEquals($unidade->id, $colaborador->unidade_id);
        
        $this->assertDatabaseHas('colaboradors', [
            'nome' => 'João da Silva',
            'cpf' => '12345678909',
            'email' => 'joao@example.com',
            'unidade_id' => $unidade->id,
        ]);
    }

    /**
     * Testa a atualização de um colaborador.
     *
     * @return void
     */
    public function test_atualizar_colaborador()
    {
        // Criar um colaborador
        $colaborador = Colaborador::factory()->create([
            'nome' => 'Nome Original',
            'cpf' => '12345678909',
            'email' => 'original@example.com',
        ]);

        // Atualizar o colaborador
        $colaborador->update([
            'nome' => 'Nome Atualizado',
            'email' => 'atualizado@example.com',
        ]);

        // Verificar se o colaborador foi atualizado corretamente
        $this->assertEquals('Nome Atualizado', $colaborador->nome);
        $this->assertEquals('atualizado@example.com', $colaborador->email);
        $this->assertDatabaseHas('colaboradors', [
            'id' => $colaborador->id,
            'nome' => 'Nome Atualizado',
            'email' => 'atualizado@example.com',
        ]);
    }

    /**
     * Testa a exclusão de um colaborador.
     *
     * @return void
     */
    public function test_excluir_colaborador()
    {
        // Criar um colaborador
        $colaborador = Colaborador::factory()->create([
            'nome' => 'Colaborador para Excluir',
            'cpf' => '12345678909',
        ]);

        // Excluir o colaborador
        $colaborador->delete();

        // Verificar se o colaborador foi excluído corretamente
        $this->assertDatabaseMissing('colaboradors', [
            'id' => $colaborador->id,
        ]);
    }

    /**
     * Testa o relacionamento com Unidade.
     *
     * @return void
     */
    public function test_relacionamento_unidade()
    {
        // Criar uma unidade
        $unidade = Unidade::factory()->create();
        
        // Criar um colaborador associado à unidade
        $colaborador = Colaborador::factory()->create([
            'unidade_id' => $unidade->id,
        ]);

        // Verificar se o relacionamento está funcionando
        $this->assertInstanceOf(Unidade::class, $colaborador->unidade);
        $this->assertEquals($unidade->id, $colaborador->unidade->id);
    }

    /**
     * Testa a validação do campo nome.
     *
     * @return void
     */
    public function test_validacao_nome_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma unidade
        $unidade = Unidade::factory()->create();
        
        // Tentar criar um colaborador sem nome (deve falhar)
        Colaborador::create([
            'cpf' => '12345678909',
            'email' => 'teste@example.com',
            'unidade_id' => $unidade->id,
        ]);
    }

    /**
     * Testa a validação do campo CPF.
     *
     * @return void
     */
    public function test_validacao_cpf_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma unidade
        $unidade = Unidade::factory()->create();
        
        // Tentar criar um colaborador sem CPF (deve falhar)
        Colaborador::create([
            'nome' => 'Colaborador sem CPF',
            'email' => 'teste@example.com',
            'unidade_id' => $unidade->id,
        ]);
    }

    /**
     * Testa a validação do campo email.
     *
     * @return void
     */
    public function test_validacao_email_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma unidade
        $unidade = Unidade::factory()->create();
        
        // Tentar criar um colaborador sem email (deve falhar)
        Colaborador::create([
            'nome' => 'Colaborador sem Email',
            'cpf' => '12345678909',
            'unidade_id' => $unidade->id,
        ]);
    }

    /**
     * Testa a validação do relacionamento com Unidade.
     *
     * @return void
     */
    public function test_validacao_unidade_obrigatoria()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Tentar criar um colaborador sem unidade (deve falhar)
        Colaborador::create([
            'nome' => 'Colaborador sem Unidade',
            'cpf' => '12345678909',
            'email' => 'teste@example.com',
        ]);
    }

    /**
     * Testa a validação de unicidade do CPF.
     *
     * @return void
     */
    public function test_validacao_cpf_unico()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma unidade
        $unidade = Unidade::factory()->create();
        
        // Criar um colaborador com um CPF
        Colaborador::create([
            'nome' => 'Colaborador 1',
            'cpf' => '12345678909',
            'email' => 'colab1@example.com',
            'unidade_id' => $unidade->id,
        ]);
        
        // Tentar criar outro colaborador com o mesmo CPF (deve falhar)
        Colaborador::create([
            'nome' => 'Colaborador 2',
            'cpf' => '12345678909',
            'email' => 'colab2@example.com',
            'unidade_id' => $unidade->id,
        ]);
    }

    /**
     * Testa a validação de unicidade do email.
     *
     * @return void
     */
    public function test_validacao_email_unico()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma unidade
        $unidade = Unidade::factory()->create();
        
        // Criar um colaborador com um email
        Colaborador::create([
            'nome' => 'Colaborador 1',
            'cpf' => '12345678909',
            'email' => 'email@example.com',
            'unidade_id' => $unidade->id,
        ]);
        
        // Tentar criar outro colaborador com o mesmo email (deve falhar)
        Colaborador::create([
            'nome' => 'Colaborador 2',
            'cpf' => '98765432100',
            'email' => 'email@example.com',
            'unidade_id' => $unidade->id,
        ]);
    }

    /**
     * Testa a formatação do CPF.
     *
     * @return void
     */
    public function test_formatacao_cpf()
    {
        // Criar um colaborador com CPF sem formatação
        $colaborador = Colaborador::factory()->create([
            'nome' => 'Colaborador com CPF Formatado',
            'cpf' => '12345678909',
        ]);

        // Verificar se o CPF foi armazenado sem formatação
        $this->assertEquals('12345678909', $colaborador->cpf);
        
        // Verificar se o CPF formatado está correto
        $cpfFormatado = substr($colaborador->cpf, 0, 3) . '.' . 
                        substr($colaborador->cpf, 3, 3) . '.' . 
                        substr($colaborador->cpf, 6, 3) . '-' . 
                        substr($colaborador->cpf, 9, 2);
        
        $this->assertEquals('123.456.789-09', $cpfFormatado);
    }
}
