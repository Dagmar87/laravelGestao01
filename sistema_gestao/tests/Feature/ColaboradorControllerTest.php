<?php

namespace Tests\Feature;

use App\Models\Colaborador;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um usuário não autenticado é redirecionado para a tela de login.
     *
     * @return void
     */
    public function test_redireciona_para_login_se_nao_autenticado()
    {
        $response = $this->get(route('colaboradores.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Testa se um usuário autenticado pode visualizar a lista de colaboradores.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_lista_de_colaboradores()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_colaborador');
        
        $response = $this->actingAs($user)->get(route('colaboradores.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('colaboradores.index');
    }

    /**
     * Testa se um usuário pode visualizar o formulário de criação de colaborador.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_criacao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $response = $this->actingAs($user)->get(route('colaboradores.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('colaboradores.create');
    }

    /**
     * Testa se um usuário pode criar um novo colaborador.
     *
     * @return void
     */
    public function test_usuario_pode_criar_colaborador()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $unidade = Unidade::factory()->create();
        
        $dados = [
            'nome' => 'João da Silva',
            'cpf' => '12345678909',
            'email' => 'joao@example.com',
            'unidade_id' => $unidade->id,
        ];
        
        $response = $this->actingAs($user)
                         ->post(route('colaboradores.store'), $dados);
        
        $response->assertRedirect(route('colaboradores.index'));
        $this->assertDatabaseHas('colaboradores', [
            'nome' => $dados['nome'],
            'cpf' => $dados['cpf'],
            'email' => $dados['email'],
            'unidade_id' => $dados['unidade_id'],
        ]);
    }

    /**
     * Testa a validação ao criar um colaborador.
     *
     * @return void
     */
    public function test_validacao_ao_criar_colaborador()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $response = $this->actingAs($user)
                         ->post(route('colaboradores.store'), []);
        
        $response->assertSessionHasErrors(['nome', 'cpf', 'email', 'unidade_id']);
    }

    /**
     * Testa se um usuário pode visualizar um colaborador específico.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_colaborador()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_colaborador');
        
        $colaborador = Colaborador::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('colaboradores.show', $colaborador->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('colaboradores.show');
        $response->assertViewHas('colaborador', $colaborador);
    }

    /**
     * Testa se um usuário pode visualizar o formulário de edição de um colaborador.
     *
     * @return void
     */
    public function test_usuario_pode_visualizar_formulario_de_edicao()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_colaborador');
        
        $colaborador = Colaborador::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('colaboradores.edit', $colaborador->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('colaboradores.edit');
        $response->assertViewHas('colaborador', $colaborador);
    }

    /**
     * Testa se um usuário pode atualizar um colaborador.
     *
     * @return void
     */
    public function test_usuario_pode_atualizar_colaborador()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_colaborador');
        
        $colaborador = Colaborador::factory()->create();
        $novaUnidade = Unidade::factory()->create();
        
        $dadosAtualizados = [
            'nome' => 'Nome Atualizado',
            'cpf' => '98765432100',
            'email' => 'novoemail@example.com',
            'unidade_id' => $novaUnidade->id,
        ];
        
        $response = $this->actingAs($user)
                         ->put(route('colaboradores.update', $colaborador->id), $dadosAtualizados);
        
        $response->assertRedirect(route('colaboradores.index'));
        $this->assertDatabaseHas('colaboradores', array_merge(['id' => $colaborador->id], $dadosAtualizados));
    }

    /**
     * Testa a validação ao atualizar um colaborador.
     *
     * @return void
     */
    public function test_validacao_ao_atualizar_colaborador()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_colaborador');
        
        $colaborador = Colaborador::factory()->create();
        
        $response = $this->actingAs($user)
                         ->put(route('colaboradores.update', $colaborador->id), [
                             'nome' => '',
                             'cpf' => '',
                             'email' => '',
                             'unidade_id' => '',
                         ]);
        
        $response->assertSessionHasErrors(['nome', 'cpf', 'email', 'unidade_id']);
    }

    /**
     * Testa se um usuário pode excluir um colaborador.
     *
     * @return void
     */
    public function test_usuario_pode_excluir_colaborador()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_colaborador');
        
        $colaborador = Colaborador::factory()->create();
        
        $response = $this->actingAs($user)
                         ->delete(route('colaboradores.destroy', $colaborador->id));
        
        $response->assertRedirect(route('colaboradores.index'));
        $this->assertDeleted($colaborador);
    }

    /**
     * Testa a autorização ao acessar rotas protegidas.
     *
     * @return void
     */
    public function test_autorizacao_ao_acessar_rotas_protegidas()
    {
        $user = User::factory()->create(); // Usuário sem permissões
        $colaborador = Colaborador::factory()->create();
        
        // Testar visualização sem permissão
        $response = $this->actingAs($user)->get(route('colaboradores.index'));
        $response->assertStatus(403);
        
        // Testar criação sem permissão
        $response = $this->actingAs($user)->get(route('colaboradores.create'));
        $response->assertStatus(403);
        
        // Testar visualização de detalhes sem permissão
        $response = $this->actingAs($user)->get(route('colaboradores.show', $colaborador->id));
        $response->assertStatus(403);
        
        // Testar edição sem permissão
        $response = $this->actingAs($user)->get(route('colaboradores.edit', $colaborador->id));
        $response->assertStatus(403);
        
        // Testar exclusão sem permissão
        $response = $this->actingAs($user)->delete(route('colaboradores.destroy', $colaborador->id));
        $response->assertStatus(403);
    }

    /**
     * Testa a validação de unicidade do CPF.
     *
     * @return void
     */
    public function test_validacao_cpf_unico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $unidade = Unidade::factory()->create();
        $cpf = '12345678909';
        
        // Criar o primeiro colaborador com o CPF
        $this->actingAs($user)
             ->post(route('colaboradores.store'), [
                 'nome' => 'Colaborador 1',
                 'cpf' => $cpf,
                 'email' => 'colab1@example.com',
                 'unidade_id' => $unidade->id,
             ]);
        
        // Tentar criar outro colaborador com o mesmo CPF
        $response = $this->actingAs($user)
                         ->post(route('colaboradores.store'), [
                             'nome' => 'Colaborador 2',
                             'cpf' => $cpf,
                             'email' => 'colab2@example.com',
                             'unidade_id' => $unidade->id,
                         ]);
        
        $response->assertSessionHasErrors(['cpf']);
        
        // Verificar se apenas um colaborador foi criado
        $this->assertEquals(1, Colaborador::where('cpf', $cpf)->count());
    }

    /**
     * Testa a validação de unicidade do e-mail.
     *
     * @return void
     */
    public function test_validacao_email_unico()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $unidade = Unidade::factory()->create();
        $email = 'mesmo@email.com';
        
        // Criar o primeiro colaborador com o e-mail
        $this->actingAs($user)
             ->post(route('colaboradores.store'), [
                 'nome' => 'Colaborador 1',
                 'cpf' => '12345678909',
                 'email' => $email,
                 'unidade_id' => $unidade->id,
             ]);
        
        // Tentar criar outro colaborador com o mesmo e-mail
        $response = $this->actingAs($user)
                         ->post(route('colaboradores.store'), [
                             'nome' => 'Colaborador 2',
                             'cpf' => '98765432100',
                             'email' => $email,
                             'unidade_id' => $unidade->id,
                         ]);
        
        $response->assertSessionHasErrors(['email']);
        
        // Verificar se apenas um colaborador foi criado com o e-mail
        $this->assertEquals(1, Colaborador::where('email', $email)->count());
    }

    /**
     * Testa a formatação do CPF ao salvar.
     *
     * @return void
     */
    public function test_formatacao_cpf_ao_salvar()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $unidade = Unidade::factory()->create();
        $cpfFormatado = '123.456.789-09';
        $cpfLimpo = '12345678909';
        
        $response = $this->actingAs($user)
                         ->post(route('colaboradores.store'), [
                             'nome' => 'Colaborador com CPF Formatado',
                             'cpf' => $cpfFormatado,
                             'email' => 'cpf@example.com',
                             'unidade_id' => $unidade->id,
                         ]);
        
        $response->assertRedirect(route('colaboradores.index'));
        $this->assertDatabaseHas('colaboradores', [
            'nome' => 'Colaborador com CPF Formatado',
            'cpf' => $cpfLimpo, // Deve estar sem formatação
        ]);
    }
}
