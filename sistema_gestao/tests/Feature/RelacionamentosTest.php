<?php

namespace Tests\Feature;

use App\Models\Bandeira;
use App\Models\Colaborador;
use App\Models\GrupoEconomico;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelacionamentosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o relacionamento completo da hierarquia de negócios.
     *
     * @return void
     */
    public function test_hierarquia_completa()
    {
        // 1. Criar um usuário com permissões
        $user = User::factory()->create();
        $user->givePermissionTo([
            'view_grupo_economico',
            'view_bandeira',
            'view_unidade',
            'view_colaborador'
        ]);

        // 2. Criar a hierarquia completa
        $grupo = GrupoEconomico::factory()->create(['nome' => 'Grupo Teste']);
        $bandeira = Bandeira::factory()->create([
            'nome' => 'Bandeira Teste',
            'grupo_economico_id' => $grupo->id
        ]);
        $unidade = Unidade::factory()->create([
            'nome_fantasia' => 'Unidade Teste',
            'razao_social' => 'Unidade Teste LTDA',
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id
        ]);
        $colaborador = Colaborador::factory()->create([
            'nome' => 'Colaborador Teste',
            'email' => 'colaborador@teste.com',
            'cpf' => '12345678909',
            'unidade_id' => $unidade->id
        ]);

        // 3. Verificar relacionamentos diretos
        $this->assertEquals(1, $grupo->bandeiras()->count());
        $this->assertEquals($bandeira->id, $grupo->bandeiras->first()->id);
        
        $this->assertEquals(1, $bandeira->unidades()->count());
        $this->assertEquals($unidade->id, $bandeira->unidades->first()->id);
        
        $this->assertEquals(1, $unidade->colaboradores()->count());
        $this->assertEquals($colaborador->id, $unidade->colaboradores->first()->id);

        // 4. Verificar relacionamentos inversos
        $this->assertEquals($bandeira->id, $unidade->bandeira->id);
        $this->assertEquals($unidade->id, $colaborador->unidade->id);
        $this->assertEquals($bandeira->id, $colaborador->unidade->bandeira->id);
        $this->assertEquals($grupo->id, $colaborador->unidade->bandeira->grupoEconomico->id);

        // 5. Verificar se as rotas de API estão protegidas
        $response = $this->actingAs($user)->get(route('grupo_economicos.show', $grupo->id));
        $response->assertStatus(200);
        
        $response = $this->actingAs($user)->get(route('bandeiras.show', $bandeira->id));
        $response->assertStatus(200);
        
        $response = $this->actingAs($user)->get(route('unidades.show', $unidade->id));
        $response->assertStatus(200);
        
        $response = $this->actingAs($user)->get(route('colaboradores.show', $colaborador->id));
        $response->assertStatus(200);
    }

    /**
     * Testa a exclusão em cascata.
     *
     * @return void
     */
    public function test_exclusao_em_cascata()
    {
        // 1. Criar a hierarquia completa
        $grupo = GrupoEconomico::factory()->create(['nome' => 'Grupo para Exclusão']);
        $bandeira = Bandeira::factory()->create(['grupo_economico_id' => $grupo->id]);
        $unidade = Unidade::factory()->create(['bandeira_id' => $bandeira->id]);
        $colaborador = Colaborador::factory()->create(['unidade_id' => $unidade->id]);

        // 2. Excluir o grupo econômico
        $grupo->delete();

        // 3. Verificar se os registros relacionados foram excluídos em cascata
        $this->assertDatabaseMissing('grupo_economicos', ['id' => $grupo->id]);
        $this->assertDatabaseMissing('bandeiras', ['id' => $bandeira->id]);
        $this->assertDatabaseMissing('unidades', ['id' => $unidade->id]);
        $this->assertDatabaseMissing('colaboradores', ['id' => $colaborador->id]);
    }

    /**
     * Testa a integridade referencial.
     *
     * @return void
     */
    public function test_integridade_referencial()
    {
        // 1. Tentar criar uma bandeira sem grupo econômico (deve falhar)
        $this->expectException(\Illuminate\Database\QueryException::class);
        Bandeira::factory()->create(['grupo_economico_id' => 999]);

        // 2. Tentar criar uma unidade sem bandeira (deve falhar)
        $this->expectException(\Illuminate\Database\QueryException::class);
        Unidade::factory()->create(['bandeira_id' => 999]);

        // 3. Tentar criar um colaborador sem unidade (deve falhar)
        $this->expectException(\Illuminate\Database\QueryException::class);
        Colaborador::factory()->create(['unidade_id' => 999]);
    }

    /**
     * Testa a contagem de relacionamentos.
     *
     * @return void
     */
    public function test_contagem_de_relacionamentos()
    {
        // 1. Criar um grupo com múltiplas bandeiras
        $grupo = GrupoEconomico::factory()->create();
        $bandeiras = Bandeira::factory()->count(3)->create([
            'grupo_economico_id' => $grupo->id
        ]);

        // 2. Para cada bandeira, criar múltiplas unidades
        $unidadesPorBandeira = [];
        foreach ($bandeiras as $bandeira) {
            $unidades = Unidade::factory()->count(2)->create([
                'bandeira_id' => $bandeira->id
            ]);
            $unidadesPorBandeira[$bandeira->id] = $unidades;

            // 3. Para cada unidade, criar múltiplos colaboradores
            foreach ($unidades as $unidade) {
                Colaborador::factory()->count(5)->create([
                    'unidade_id' => $unidade->id
                ]);
            }
        }

        // 4. Verificar contagens
        $this->assertEquals(3, $grupo->bandeiras()->count());
        
        foreach ($bandeiras as $bandeira) {
            $this->assertEquals(2, $bandeira->unidades()->count());
            
            foreach ($unidadesPorBandeira[$bandeira->id] as $unidade) {
                $this->assertEquals(5, $unidade->colaboradores()->count());
            }
        }
    }

    /**
     * Testa a atualização em cascata de relacionamentos.
     *
     * @return void
     */
    public function test_atualizacao_em_cascata()
    {
        // 1. Criar hierarquia completa
        $grupo1 = GrupoEconomico::factory()->create(['nome' => 'Grupo 1']);
        $grupo2 = GrupoEconomico::factory()->create(['nome' => 'Grupo 2']);
        
        $bandeira = Bandeira::factory()->create(['grupo_economico_id' => $grupo1->id]);
        $unidade = Unidade::factory()->create(['bandeira_id' => $bandeira->id]);
        $colaborador = Colaborador::factory()->create(['unidade_id' => $unidade->id]);

        // 2. Atualizar a bandeira para outro grupo
        $bandeira->update(['grupo_economico_id' => $grupo2->id]);
        
        // 3. Verificar se a atualização não afetou os relacionamentos
        $this->assertEquals($grupo2->id, $bandeira->fresh()->grupo_economico_id);
        $this->assertEquals($bandeira->id, $unidade->fresh()->bandeira_id);
        $this->assertEquals($unidade->id, $colaborador->fresh()->unidade_id);
    }
}
