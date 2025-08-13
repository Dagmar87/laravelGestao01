<?php

namespace Tests\Unit;

use App\Models\Bandeira;
use App\Models\GrupoEconomico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BandeiraTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de uma bandeira.
     *
     * @return void
     */
    public function test_criar_bandeira()
    {
        // Criar um grupo econômico para associar à bandeira
        $grupo = GrupoEconomico::factory()->create();

        // Criar uma bandeira
        $bandeira = Bandeira::create([
            'nome' => 'Bandeira de Teste',
            'grupo_economico_id' => $grupo->id,
        ]);

        // Verificar se a bandeira foi criada corretamente
        $this->assertInstanceOf(Bandeira::class, $bandeira);
        $this->assertEquals('Bandeira de Teste', $bandeira->nome);
        $this->assertEquals($grupo->id, $bandeira->grupo_economico_id);
        $this->assertDatabaseHas('bandeiras', [
            'nome' => 'Bandeira de Teste',
            'grupo_economico_id' => $grupo->id,
        ]);
    }

    /**
     * Testa a atualização de uma bandeira.
     *
     * @return void
     */
    public function test_atualizar_bandeira()
    {
        // Criar uma bandeira
        $bandeira = Bandeira::factory()->create([
            'nome' => 'Bandeira Original',
        ]);

        // Atualizar a bandeira
        $bandeira->update(['nome' => 'Bandeira Atualizada']);

        // Verificar se a bandeira foi atualizada corretamente
        $this->assertEquals('Bandeira Atualizada', $bandeira->nome);
        $this->assertDatabaseHas('bandeiras', [
            'id' => $bandeira->id,
            'nome' => 'Bandeira Atualizada',
        ]);
    }

    /**
     * Testa a exclusão de uma bandeira.
     *
     * @return void
     */
    public function test_excluir_bandeira()
    {
        // Criar uma bandeira
        $bandeira = Bandeira::factory()->create([
            'nome' => 'Bandeira para Excluir',
        ]);

        // Excluir a bandeira
        $bandeira->delete();

        // Verificar se a bandeira foi excluída corretamente
        $this->assertDatabaseMissing('bandeiras', [
            'id' => $bandeira->id,
        ]);
    }

    /**
     * Testa o relacionamento com Grupo Econômico.
     *
     * @return void
     */
    public function test_relacionamento_grupo_economico()
    {
        // Criar um grupo econômico
        $grupo = GrupoEconomico::factory()->create();
        
        // Criar uma bandeira associada ao grupo
        $bandeira = Bandeira::factory()->create([
            'grupo_economico_id' => $grupo->id,
        ]);

        // Verificar se o relacionamento está funcionando
        $this->assertInstanceOf(GrupoEconomico::class, $bandeira->grupoEconomico);
        $this->assertEquals($grupo->id, $bandeira->grupoEconomico->id);
    }

    /**
     * Testa o relacionamento com Unidades.
     *
     * @return void
     */
    public function test_relacionamento_unidades()
    {
        // Criar uma bandeira com unidades
        $bandeira = Bandeira::factory()
            ->hasUnidades(2)
            ->create();

        // Verificar se o relacionamento está funcionando
        $this->assertCount(2, $bandeira->unidades);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $bandeira->unidades);
    }

    /**
     * Testa a validação do campo nome.
     *
     * @return void
     */
    public function test_validacao_nome_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar um grupo econômico
        $grupo = GrupoEconomico::factory()->create();
        
        // Tentar criar uma bandeira sem nome (deve falhar)
        Bandeira::create([
            'grupo_economico_id' => $grupo->id,
        ]);
    }

    /**
     * Testa a validação do relacionamento com Grupo Econômico.
     *
     * @return void
     */
    public function test_validacao_grupo_economico_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Tentar criar uma bandeira sem grupo econômico (deve falhar)
        Bandeira::create([
            'nome' => 'Bandeira sem Grupo',
        ]);
    }

    /**
     * Testa a validação de unicidade do nome por grupo econômico.
     *
     * @return void
     */
    public function test_validacao_nome_unico_por_grupo()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar um grupo econômico
        $grupo = GrupoEconomico::factory()->create();
        
        // Criar uma bandeira com um nome
        Bandeira::create([
            'nome' => 'Bandeira Duplicada',
            'grupo_economico_id' => $grupo->id,
        ]);
        
        // Tentar criar outra bandeira com o mesmo nome no mesmo grupo (deve falhar)
        Bandeira::create([
            'nome' => 'Bandeira Duplicada',
            'grupo_economico_id' => $grupo->id,
        ]);
    }

    /**
     * Testa que é permitido ter bandeiras com o mesmo nome em grupos diferentes.
     *
     * @return void
     */
    public function test_permitir_nome_igual_em_grupos_diferentes()
    {
        // Criar dois grupos econômicos
        $grupo1 = GrupoEconomico::factory()->create();
        $grupo2 = GrupoEconomico::factory()->create();
        
        // Criar bandeiras com o mesmo nome em grupos diferentes
        $bandeira1 = Bandeira::create([
            'nome' => 'Bandeira com Nome Igual',
            'grupo_economico_id' => $grupo1->id,
        ]);
        
        $bandeira2 = Bandeira::create([
            'nome' => 'Bandeira com Nome Igual',
            'grupo_economico_id' => $grupo2->id,
        ]);
        
        // Verificar se ambas as bandeiras foram criadas
        $this->assertDatabaseHas('bandeiras', [
            'id' => $bandeira1->id,
            'nome' => 'Bandeira com Nome Igual',
            'grupo_economico_id' => $grupo1->id,
        ]);
        
        $this->assertDatabaseHas('bandeiras', [
            'id' => $bandeira2->id,
            'nome' => 'Bandeira com Nome Igual',
            'grupo_economico_id' => $grupo2->id,
        ]);
    }
}
