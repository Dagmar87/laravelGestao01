<?php

namespace Tests\Feature;

use App\Models\GrupoEconomico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrupoEconomicoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um usuário não autenticado é redirecionado para a tela de login.
     *
     * @return void
     */
    public function test_redireciona_para_login_se_nao_autenticado()
    {
        $response = $this->get(route('grupo_economicos.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Testa se um usuário autenticado pode visualizar a lista de grupos econômicos.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_lista_de_grupos_economicos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_grupo_economico');
        
        $response = $this->actingAs($user)->get(route('grupo_economicos.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('grupo_economicos.index');
    }

    /**
     * Testa se um usuário pode visualizar o formulário de criação de grupo econômico.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_criacao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_grupo_economico');
        
        $response = $this->actingAs($user)->get(route('grupo_economicos.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('grupo_economicos.create');
    }

    /**
     * Testa se um usuário pode criar um novo grupo econômico.
     *
     * @return void
     */
    public function test_usuario_pode_criar_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_grupo_economico');
        
        $dados = [
            'nome' => 'Novo Grupo Econômico',
        ];
        
        $response = $this->actingAs($user)
                         ->post(route('grupo_economicos.store'), $dados);
        
        $response->assertRedirect(route('grupo_economicos.index'));
        $this->assertDatabaseHas('grupo_economicos', $dados);
    }

    /**
     * Testa a validação ao criar um grupo econômico.
     *
     * @return void
     */
    public function test_validacao_ao_criar_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_grupo_economico');
        
        $response = $this->actingAs($user)
                         ->post(route('grupo_economicos.store'), []);
        
        $response->assertSessionHasErrors(['nome']);
    }

    /**
     * Testa se um usuário pode visualizar um grupo econômico específico.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_grupo_economico');
        
        $grupo = GrupoEconomico::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('grupo_economicos.show', $grupo->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('grupo_economicos.show');
        $response->assertViewHas('grupo_economico', $grupo);
    }

    /**
     * Testa se um usuário pode visualizar o formulário de edição de um grupo econômico.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_edicao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_grupo_economico');
        
        $grupo = GrupoEconomico::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('grupo_economicos.edit', $grupo->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('grupo_economicos.edit');
        $response->assertViewHas('grupo_economico', $grupo);
    }

    /**
     * Testa se um usuário pode atualizar um grupo econômico.
     *
     * @return void
     */
    public function test_usuario_pode_atualizar_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_grupo_economico');
        
        $grupo = GrupoEconomico::factory()->create();
        
        $dadosAtualizados = [
            'nome' => 'Nome Atualizado',
        ];
        
        $response = $this->actingAs($user)
                         ->put(route('grupo_economicos.update', $grupo->id), $dadosAtualizados);
        
        $response->assertRedirect(route('grupo_economicos.index'));
        $this->assertDatabaseHas('grupo_economicos', $dadosAtualizados);
    }

    /**
     * Testa a validação ao atualizar um grupo econômico.
     *
     * @return void
     */
    public function test_validacao_ao_atualizar_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_grupo_economico');
        
        $grupo = GrupoEconomico::factory()->create();
        
        $response = $this->actingAs($user)
                         ->put(route('grupo_economicos.update', $grupo->id), ['nome' => '']);
        
        $response->assertSessionHasErrors(['nome']);
    }

    /**
     * Testa se um usuário pode excluir um grupo econômico.
     *
     * @return void
     */
    public function test_usuario_pode_excluir_grupo_economico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_grupo_economico');
        
        $grupo = GrupoEconomico::factory()->create();
        
        $response = $this->actingAs($user)
                         ->delete(route('grupo_economicos.destroy', $grupo->id));
        
        $response->assertRedirect(route('grupo_economicos.index'));
        $this->assertDeleted($grupo);
    }

    /**
     * Testa a autorização ao acessar rotas protegidas.
     *
     * @return void
     */
    public function test_autorizacao_ao_acessar_rotas_protegidas()
    {
        $user = User::factory()->create(); // Usuário sem permissões
        $grupo = GrupoEconomico::factory()->create();
        
        // Testar visualização sem permissão
        $response = $this->actingAs($user)->get(route('grupo_economicos.index'));
        $response->assertStatus(403);
        
        // Testar criação sem permissão
        $response = $this->actingAs($user)->get(route('grupo_economicos.create'));
        $response->assertStatus(403);
        
        // Testar edição sem permissão
        $response = $this->actingAs($user)->get(route('grupo_economicos.edit', $grupo->id));
        $response->assertStatus(403);
        
        // Testar exclusão sem permissão
        $response = $this->actingAs($user)->delete(route('grupo_economicos.destroy', $grupo->id));
        $response->assertStatus(403);
    }
}
