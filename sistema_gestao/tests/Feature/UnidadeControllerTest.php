<?php

namespace Tests\Feature;

use App\Models\Bandeira;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um usuário não autenticado é redirecionado para a tela de login.
     *
     * @return void
     */
    public function test_redireciona_para_login_se_nao_autenticado()
    {
        $response = $this->get(route('unidades.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Testa se um usuário autenticado pode visualizar a lista de unidades.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_lista_de_unidades()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_unidade');
        
        $response = $this->actingAs($user)->get(route('unidades.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('unidades.index');
    }

    /**
     * Testa se um usuário pode visualizar o formulário de criação de unidade.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_criacao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_unidade');
        
        $response = $this->actingAs($user)->get(route('unidades.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('unidades.create');
    }

    /**
     * Testa se um usuário pode criar uma nova unidade.
     *
     * @return void
     */
    public function test_usuario_pode_criar_unidade()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_unidade');
        
        $bandeira = Bandeira::factory()->create();
        
        $dados = [
            'nome' => 'Nova Unidade',
            'cnpj' => '12345678000190',
            'bandeira_id' => $bandeira->id,
        ];
        
        $response = $this->actingAs($user)
                         ->post(route('unidades.store'), $dados);
        
        $response->assertRedirect(route('unidades.index'));
        $this->assertDatabaseHas('unidades', [
            'nome' => $dados['nome'],
            'cnpj' => $dados['cnpj'],
            'bandeira_id' => $dados['bandeira_id'],
        ]);
    }

    /**
     * Testa a validação ao criar uma unidade.
     *
     * @return void
     */
    public function test_validacao_ao_criar_unidade()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_unidade');
        
        $response = $this->actingAs($user)
                         ->post(route('unidades.store'), []);
        
        $response->assertSessionHasErrors(['nome', 'cnpj', 'bandeira_id']);
    }

    /**
     * Testa se um usuário pode visualizar uma unidade específica.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_unidade()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_unidade');
        
        $unidade = Unidade::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('unidades.show', $unidade->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('unidades.show');
        $response->assertViewHas('unidade', $unidade);
    }

    /**
     * Testa se um usuário pode visualizar o formulário de edição de uma unidade.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_edicao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_unidade');
        
        $unidade = Unidade::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('unidades.edit', $unidade->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('unidades.edit');
        $response->assertViewHas('unidade', $unidade);
    }

    /**
     * Testa se um usuário pode atualizar uma unidade.
     *
     * @return void
     */
    public function test_usuario_pode_atualizar_unidade()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_unidade');
        
        $unidade = Unidade::factory()->create();
        $novaBandeira = Bandeira::factory()->create();
        
        $dadosAtualizados = [
            'nome' => 'Nome Atualizado',
            'cnpj' => '98765432000198',
            'bandeira_id' => $novaBandeira->id,
        ];
        
        $response = $this->actingAs($user)
                         ->put(route('unidades.update', $unidade->id), $dadosAtualizados);
        
        $response->assertRedirect(route('unidades.index'));
        $this->assertDatabaseHas('unidades', array_merge(['id' => $unidade->id], $dadosAtualizados));
    }

    /**
     * Testa a validação ao atualizar uma unidade.
     *
     * @return void
     */
    public function test_validacao_ao_atualizar_unidade()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_unidade');
        
        $unidade = Unidade::factory()->create();
        
        $response = $this->actingAs($user)
                         ->put(route('unidades.update', $unidade->id), [
                             'nome' => '',
                             'cnpj' => '',
                             'bandeira_id' => '',
                         ]);
        
        $response->assertSessionHasErrors(['nome', 'cnpj', 'bandeira_id']);
    }

    /**
     * Testa se um usuário pode excluir uma unidade.
     *
     * @return void
     */
    public function test_usuario_pode_excluir_unidade()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_unidade');
        
        $unidade = Unidade::factory()->create();
        
        $response = $this->actingAs($user)
                         ->delete(route('unidades.destroy', $unidade->id));
        
        $response->assertRedirect(route('unidades.index'));
        $this->assertDeleted($unidade);
    }

    /**
     * Testa a autorização ao acessar rotas protegidas.
     *
     * @return void
     */
    public function test_autorizacao_ao_acessar_rotas_protegidas()
    {
        $user = User::factory()->create(); // Usuário sem permissões
        $unidade = Unidade::factory()->create();
        
        // Testar visualização sem permissão
        $response = $this->actingAs($user)->get(route('unidades.index'));
        $response->assertStatus(403);
        
        // Testar criação sem permissão
        $response = $this->actingAs($user)->get(route('unidades.create'));
        $response->assertStatus(403);
        
        // Testar visualização de detalhes sem permissão
        $response = $this->actingAs($user)->get(route('unidades.show', $unidade->id));
        $response->assertStatus(403);
        
        // Testar edição sem permissão
        $response = $this->actingAs($user)->get(route('unidades.edit', $unidade->id));
        $response->assertStatus(403);
        
        // Testar exclusão sem permissão
        $response = $this->actingAs($user)->delete(route('unidades.destroy', $unidade->id));
        $response->assertStatus(403);
    }

    /**
     * Testa a validação de unicidade do CNPJ.
     *
     * @return void
     */
    public function test_validacao_cnpj_unico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_unidade');
        
        $bandeira = Bandeira::factory()->create();
        $cnpj = '12345678000190';
        
        // Criar a primeira unidade com o CNPJ
        $this->actingAs($user)
             ->post(route('unidades.store'), [
                 'nome' => 'Unidade 1',
                 'cnpj' => $cnpj,
                 'bandeira_id' => $bandeira->id,
             ]);
        
        // Tentar criar outra unidade com o mesmo CNPJ
        $response = $this->actingAs($user)
                         ->post(route('unidades.store'), [
                             'nome' => 'Unidade 2',
                             'cnpj' => $cnpj,
                             'bandeira_id' => $bandeira->id,
                         ]);
        
        $response->assertSessionHasErrors(['cnpj']);
        
        // Verificar se apenas uma unidade foi criada
        $this->assertEquals(1, Unidade::where('cnpj', $cnpj)->count());
    }

    /**
     * Testa a formatação do CNPJ ao salvar.
     *
     * @return void
     */
    public function test_formatacao_cnpj_ao_salvar()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_unidade');
        
        $bandeira = Bandeira::factory()->create();
        $cnpjFormatado = '12.345.678/0001-90';
        $cnpjLimpo = '12345678000190';
        
        $response = $this->actingAs($user)
                         ->post(route('unidades.store'), [
                             'nome' => 'Unidade com CNPJ Formatado',
                             'cnpj' => $cnpjFormatado,
                             'bandeira_id' => $bandeira->id,
                         ]);
        
        $response->assertRedirect(route('unidades.index'));
        $this->assertDatabaseHas('unidades', [
            'nome' => 'Unidade com CNPJ Formatado',
            'cnpj' => $cnpjLimpo, // Deve estar sem formatação
        ]);
    }
}
