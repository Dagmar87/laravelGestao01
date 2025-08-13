<?php

namespace Tests\Feature;

use App\Models\Bandeira;
use App\Models\GrupoEconomico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BandeiraControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um usuário não autenticado é redirecionado para a tela de login.
     *
     * @return void
     */
    public function test_redireciona_para_login_se_nao_autenticado()
    {
        $response = $this->get(route('bandeiras.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Testa se um usuário autenticado pode visualizar a lista de bandeiras.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_lista_de_bandeiras()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_bandeira');
        
        $response = $this->actingAs($user)->get(route('bandeiras.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('bandeiras.index');
    }

    /**
     * Testa se um usuário pode visualizar o formulário de criação de bandeira.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_criacao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_bandeira');
        
        $response = $this->actingAs($user)->get(route('bandeiras.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('bandeiras.create');
    }

    /**
     * Testa se um usuário pode criar uma nova bandeira.
     *
     * @return void
     */
    public function test_usuario_pode_criar_bandeira()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_bandeira');
        
        $grupo = GrupoEconomico::factory()->create();
        
        $dados = [
            'nome' => 'Nova Bandeira',
            'grupo_economico_id' => $grupo->id,
        ];
        
        $response = $this->actingAs($user)
                         ->post(route('bandeiras.store'), $dados);
        
        $response->assertRedirect(route('bandeiras.index'));
        $this->assertDatabaseHas('bandeiras', $dados);
    }

    /**
     * Testa a validação ao criar uma bandeira.
     *
     * @return void
     */
    public function test_validacao_ao_criar_bandeira()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_bandeira');
        
        $response = $this->actingAs($user)
                         ->post(route('bandeiras.store'), []);
        
        $response->assertSessionHasErrors(['nome', 'grupo_economico_id']);
    }

    /**
     * Testa se um usuário pode visualizar uma bandeira específica.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_bandeira()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_bandeira');
        
        $bandeira = Bandeira::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('bandeiras.show', $bandeira->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('bandeiras.show');
        $response->assertViewHas('bandeira', $bandeira);
    }

    /**
     * Testa se um usuário pode visualizar o formulário de edição de uma bandeira.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_edicao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_bandeira');
        
        $bandeira = Bandeira::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('bandeiras.edit', $bandeira->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('bandeiras.edit');
        $response->assertViewHas('bandeira', $bandeira);
    }

    /**
     * Testa se um usuário pode atualizar uma bandeira.
     *
     * @return void
     */
    public function test_usuario_pode_atualizar_bandeira()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_bandeira');
        
        $bandeira = Bandeira::factory()->create();
        $novoGrupo = GrupoEconomico::factory()->create();
        
        $dadosAtualizados = [
            'nome' => 'Nome Atualizado',
            'grupo_economico_id' => $novoGrupo->id,
        ];
        
        $response = $this->actingAs($user)
                         ->put(route('bandeiras.update', $bandeira->id), $dadosAtualizados);
        
        $response->assertRedirect(route('bandeiras.index'));
        $this->assertDatabaseHas('bandeiras', array_merge(['id' => $bandeira->id], $dadosAtualizados));
    }

    /**
     * Testa a validação ao atualizar uma bandeira.
     *
     * @return void
     */
    public function test_validacao_ao_atualizar_bandeira()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_bandeira');
        
        $bandeira = Bandeira::factory()->create();
        
        $response = $this->actingAs($user)
                         ->put(route('bandeiras.update', $bandeira->id), [
                             'nome' => '',
                             'grupo_economico_id' => '',
                         ]);
        
        $response->assertSessionHasErrors(['nome', 'grupo_economico_id']);
    }

    /**
     * Testa se um usuário pode excluir uma bandeira.
     *
     * @return void
     */
    public function test_usuario_pode_excluir_bandeira()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_bandeira');
        
        $bandeira = Bandeira::factory()->create();
        
        $response = $this->actingAs($user)
                         ->delete(route('bandeiras.destroy', $bandeira->id));
        
        $response->assertRedirect(route('bandeiras.index'));
        $this->assertDeleted($bandeira);
    }

    /**
     * Testa a autorização ao acessar rotas protegidas.
     *
     * @return void
     */
    public function test_autorizacao_ao_acessar_rotas_protegidas()
    {
        $user = User::factory()->create(); // Usuário sem permissões
        $bandeira = Bandeira::factory()->create();
        
        // Testar visualização sem permissão
        $response = $this->actingAs($user)->get(route('bandeiras.index'));
        $response->assertStatus(403);
        
        // Testar criação sem permissão
        $response = $this->actingAs($user)->get(route('bandeiras.create'));
        $response->assertStatus(403);
        
        // Testar visualização de detalhes sem permissão
        $response = $this->actingAs($user)->get(route('bandeiras.show', $bandeira->id));
        $response->assertStatus(403);
        
        // Testar edição sem permissão
        $response = $this->actingAs($user)->get(route('bandeiras.edit', $bandeira->id));
        $response->assertStatus(403);
        
        // Testar exclusão sem permissão
        $response = $this->actingAs($user)->delete(route('bandeiras.destroy', $bandeira->id));
        $response->assertStatus(403);
    }

    /**
     * Testa a validação de unicidade do nome da bandeira por grupo econômico.
     *
     * @return void
     */
    public function test_validacao_nome_unico_por_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_bandeira');
        
        $grupo = GrupoEconomico::factory()->create();
        $nomeBandeira = 'Bandeira Teste';
        
        // Criar a primeira bandeira
        $this->actingAs($user)
             ->post(route('bandeiras.store'), [
                 'nome' => $nomeBandeira,
                 'grupo_economico_id' => $grupo->id,
             ]);
        
        // Tentar criar outra bandeira com o mesmo nome no mesmo grupo
        $response = $this->actingAs($user)
                         ->post(route('bandeiras.store'), [
                             'nome' => $nomeBandeira,
                             'grupo_economico_id' => $grupo->id,
                         ]);
        
        $response->assertSessionHasErrors(['nome']);
        
        // Verificar se apenas uma bandeira foi criada
        $this->assertEquals(1, Bandeira::where('nome', $nomeBandeira)
                                      ->where('grupo_economico_id', $grupo->id)
                                      ->count());
    }

    /**
     * Testa se é permitido ter bandeiras com o mesmo nome em grupos diferentes.
     *
     * @return void
     */
    public function test_permitir_nome_igual_em_grupos_diferentes()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_bandeira');
        
        $grupo1 = GrupoEconomico::factory()->create();
        $grupo2 = GrupoEconomico::factory()->create();
        $nomeBandeira = 'Bandeira Teste';
        
        // Criar a primeira bandeira no grupo 1
        $this->actingAs($user)
             ->post(route('bandeiras.store'), [
                 'nome' => $nomeBandeira,
                 'grupo_economico_id' => $grupo1->id,
             ]);
        
        // Criar a segunda bandeira no grupo 2 com o mesmo nome
        $response = $this->actingAs($user)
                         ->post(route('bandeiras.store'), [
                             'nome' => $nomeBandeira,
                             'grupo_economico_id' => $grupo2->id,
                         ]);
        
        $response->assertRedirect(route('bandeiras.index'));
        
        // Verificar se ambas as bandeiras foram criadas
        $this->assertEquals(1, Bandeira::where('nome', $nomeBandeira)
                                      ->where('grupo_economico_id', $grupo1->id)
                                      ->count());
        
        $this->assertEquals(1, Bandeira::where('nome', $nomeBandeira)
                                      ->where('grupo_economico_id', $grupo2->id)
                                      ->count());
    }
}
