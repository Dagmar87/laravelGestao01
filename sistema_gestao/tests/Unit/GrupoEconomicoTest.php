<?php

namespace Tests\Unit;

use App\Models\GrupoEconomico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrupoEconomicoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um grupo econômico.
     *
     * @return void
     */
    public function test_criar_grupo_economico()
    {
        // Criar um grupo econômico
        $grupo = GrupoEconomico::create([
            'nome' => 'Grupo de Teste',
        ]);

        // Verificar se o grupo foi criado corretamente
        $this->assertInstanceOf(GrupoEconomico::class, $grupo);
        $this->assertEquals('Grupo de Teste', $grupo->nome);
        $this->assertDatabaseHas('grupo_economicos', [
            'nome' => 'Grupo de Teste',
        ]);
    }

    /**
     * Testa a atualização de um grupo econômico.
     *
     * @return void
     */
    public function test_atualizar_grupo_economico()
    {
        // Criar um grupo econômico
        $grupo = GrupoEconomico::factory()->create([
            'nome' => 'Grupo Original',
        ]);

        // Atualizar o grupo
        $grupo->update(['nome' => 'Grupo Atualizado']);

        // Verificar se o grupo foi atualizado corretamente
        $this->assertEquals('Grupo Atualizado', $grupo->nome);
        $this->assertDatabaseHas('grupo_economicos', [
            'id' => $grupo->id,
            'nome' => 'Grupo Atualizado',
        ]);
    }

    /**
     * Testa a exclusão de um grupo econômico.
     *
     * @return void
     */
    public function test_excluir_grupo_economico()
    {
        // Criar um grupo econômico
        $grupo = GrupoEconomico::factory()->create([
            'nome' => 'Grupo para Excluir',
        ]);

        // Excluir o grupo
        $grupo->delete();

        // Verificar se o grupo foi excluído corretamente
        $this->assertDatabaseMissing('grupo_economicos', [
            'id' => $grupo->id,
        ]);
    }

    /**
     * Testa o relacionamento com bandeiras.
     *
     * @return void
     */
    public function test_relacionamento_bandeiras()
    {
        // Criar um grupo econômico
        $grupo = GrupoEconomico::factory()
            ->hasBandeiras(3)
            ->create();

        // Verificar se o relacionamento está funcionando
        $this->assertCount(3, $grupo->bandeiras);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $grupo->bandeiras);
    }

    /**
     * Testa a validação do campo nome.
     *
     * @return void
     */
    public function test_validacao_nome_obrigatorio()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Tentar criar um grupo sem nome (deve falhar)
        GrupoEconomico::create([]);
    }

    /**
     * Testa a validação de unicidade do nome.
     *
     * @return void
     */
    public function test_validacao_nome_unico()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        // Criar um grupo com um nome
        GrupoEconomico::create(['nome' => 'Grupo Duplicado']);
        
        // Tentar criar outro grupo com o mesmo nome (deve falhar)
        GrupoEconomico::create(['nome' => 'Grupo Duplicado']);
    }
}
