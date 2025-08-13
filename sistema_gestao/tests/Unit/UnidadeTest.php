<?php

namespace Tests\Unit;

use App\Models\Unidade;
use App\Models\Bandeira;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de uma unidade.
     *
     * @return void
     */
    public function test_criar_unidade()
    {
        // Criar uma bandeira para associar à unidade
        $bandeira = Bandeira::factory()->create();

        // Criar uma unidade
        $unidade = Unidade::create([
            'nome' => 'Unidade de Teste',
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id,
        ]);

        // Verificar se a unidade foi criada corretamente
        $this->assertInstanceOf(Unidade::class, $unidade);
        $this->assertEquals('Unidade de Teste', $unidade->nome);
        $this->assertEquals('12345678000190', $unidade->cnpj);
        $this->assertEquals($bandeira->id, $unidade->bandeira_id);
        $this->assertDatabaseHas('unidades', [
            'nome' => 'Unidade de Teste',
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id,
        ]);
    }

    /**
     * Testa a atualização de uma unidade.
     *
     * @return void
     */
    public function test_atualizar_unidade()
    {
        // Criar uma unidade
        $unidade = Unidade::factory()->create([
            'nome' => 'Unidade Original',
            'cnpj' => '12345678000190',
        ]);

        // Atualizar a unidade
        $unidade->update([
            'nome' => 'Unidade Atualizada',
            'cnpj' => '98765432000198',
        ]);

        // Verificar se a unidade foi atualizada corretamente
        $this->assertEquals('Unidade Atualizada', $unidade->nome);
        $this->assertEquals('98765432000198', $unidade->cnpj);
        $this->assertDatabaseHas('unidades', [
            'id' => $unidade->id,
            'nome' => 'Unidade Atualizada',
            'cnpj' => '98765432000198',
        ]);
    }

    /**
     * Testa a exclusão de uma unidade.
     *
     * @return void
     */
    public function test_excluir_unidade()
    {
        // Criar uma unidade
        $unidade = Unidade::factory()->create([
            'nome' => 'Unidade para Excluir',
            'cnpj' => '12345678000190',
        ]);

        // Excluir a unidade
        $unidade->delete();

        // Verificar se a unidade foi excluída corretamente
        $this->assertDatabaseMissing('unidades', [
            'id' => $unidade->id,
        ]);
    }

    /**
     * Testa o relacionamento com Bandeira.
     *
     * @return void
     */
    public function test_relacionamento_bandeira()
    {
        // Criar uma bandeira
        $bandeira = Bandeira::factory()->create();
        
        // Criar uma unidade associada à bandeira
        $unidade = Unidade::factory()->create([
            'bandeira_id' => $bandeira->id,
        ]);

        // Verificar se o relacionamento está funcionando
        $this->assertInstanceOf(Bandeira::class, $unidade->bandeira);
        $this->assertEquals($bandeira->id, $unidade->bandeira->id);
    }

    /**
     * Testa o relacionamento com Colaboradores.
     *
     * @return void
     */
    public function test_relacionamento_colaboradores()
    {
        // Criar uma unidade com colaboradores
        $unidade = Unidade::factory()
            ->hasColaboradores(3)
            ->create();

        // Verificar se o relacionamento está funcionando
        $this->assertCount(3, $unidade->colaboradores);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $unidade->colaboradores);
    }

    /**
     * Testa a validação do campo nome.
     *
     * @return void
     */
    public function test_validacao_nome_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma bandeira
        $bandeira = Bandeira::factory()->create();
        
        // Tentar criar uma unidade sem nome (deve falhar)
        Unidade::create([
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id,
        ]);
    }

    /**
     * Testa a validação do campo CNPJ.
     *
     * @return void
     */
    public function test_validacao_cnpj_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma bandeira
        $bandeira = Bandeira::factory()->create();
        
        // Tentar criar uma unidade sem CNPJ (deve falhar)
        Unidade::create([
            'nome' => 'Unidade sem CNPJ',
            'bandeira_id' => $bandeira->id,
        ]);
    }

    /**
     * Testa a validação do relacionamento com Bandeira.
     *
     * @return void
     */
    public function test_validacao_bandeira_obrigatoria()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Tentar criar uma unidade sem bandeira (deve falhar)
        Unidade::create([
            'nome' => 'Unidade sem Bandeira',
            'cnpj' => '12345678000190',
        ]);
    }

    /**
     * Testa a validação de unicidade do CNPJ.
     *
     * @return void
     */
    public function test_validacao_cnpj_unico()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar uma bandeira
        $bandeira = Bandeira::factory()->create();
        
        // Criar uma unidade com um CNPJ
        Unidade::create([
            'nome' => 'Unidade 1',
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id,
        ]);
        
        // Tentar criar outra unidade com o mesmo CNPJ (deve falhar)
        Unidade::create([
            'nome' => 'Unidade 2',
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id,
        ]);
    }

    /**
     * Testa a formatação do CNPJ.
     *
     * @return void
     */
    public function test_formatacao_cnpj()
    {
        // Criar uma unidade com CNPJ sem formatação
        $unidade = Unidade::factory()->create([
            'nome' => 'Unidade com CNPJ Formatado',
            'cnpj' => '12345678000190',
        ]);

        // Verificar se o CNPJ foi armazenado sem formatação
        $this->assertEquals('12345678000190', $unidade->cnpj);
        
        // Verificar se o CNPJ formatado está correto
        $cnpjFormatado = substr($unidade->cnpj, 0, 2) . '.' . 
                         substr($unidade->cnpj, 2, 3) . '.' . 
                         substr($unidade->cnpj, 5, 3) . '/' . 
                         substr($unidade->cnpj, 8, 4) . '-' . 
                         substr($unidade->cnpj, 12, 2);
        
        $this->assertEquals('12.345.678/0001-90', $cnpjFormatado);
    }
}
