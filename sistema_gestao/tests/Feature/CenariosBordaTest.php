<?php

namespace Tests\Feature;

use App\Models\Bandeira;
use App\Models\Colaborador;
use App\Models\GrupoEconomico;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CenariosBordaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um grupo econômico com nome muito longo.
     *
     * @return void
     */
    public function test_criar_grupo_economico_com_nome_muito_longo()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_grupo_economico');

        $response = $this->actingAs($user)->post(route('grupo_economicos.store'), [
            'nome' => Str::random(256) // Nome com mais de 255 caracteres
        ]);

        $response->assertSessionHasErrors(['nome']);
    }

    /**
     * Testa a criação de um colaborador com CPF inválido.
     *
     * @return void
     */
    public function test_criar_colaborador_com_cpf_invalido()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_colaborador');
        
        $unidade = Unidade::factory()->create();

        $response = $this->actingAs($user)->post(route('colaboradores.store'), [
            'nome' => 'Teste CPF Inválido',
            'email' => 'teste@example.com',
            'cpf' => '123.456.789-00', // CPF inválido
            'unidade_id' => $unidade->id
        ]);

        $response->assertSessionHasErrors(['cpf']);
    }

    /**
     * Testa a criação de uma unidade com CNPJ já existente.
     *
     * @return void
     */
    public function test_criar_unidade_com_cnpj_duplicado()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_unidade');
        
        $bandeira1 = Bandeira::factory()->create();
        $bandeira2 = Bandeira::factory()->create();
        
        $cnpj = '12345678000190';
        
        // Criar primeira unidade
        $this->actingAs($user)->post(route('unidades.store'), [
            'nome_fantasia' => 'Unidade 1',
            'razao_social' => 'Unidade 1 LTDA',
            'cnpj' => $cnpj,
            'bandeira_id' => $bandeira1->id
        ]);
        
        // Tentar criar segunda unidade com o mesmo CNPJ
        $response = $this->actingAs($user)->post(route('unidades.store'), [
            'nome_fantasia' => 'Unidade 2',
            'razao_social' => 'Unidade 2 LTDA',
            'cnpj' => $cnpj, // Mesmo CNPJ
            'bandeira_id' => $bandeira2->id
        ]);
        
        $response->assertSessionHasErrors(['cnpj']);
    }

    /**
     * Testa a exclusão de um grupo econômico com relacionamentos.
     *
     * @return void
     */
    public function test_excluir_grupo_economico_com_relacionamentos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_grupo_economico', 'view_grupo_economico']);
        
        // Criar hierarquia completa
        $grupo = GrupoEconomico::factory()->create();
        $bandeira = Bandeira::factory()->create(['grupo_economico_id' => $grupo->id]);
        $unidade = Unidade::factory()->create(['bandeira_id' => $bandeira->id]);
        Colaborador::factory()->create(['unidade_id' => $unidade->id]);
        
        // Tentar excluir o grupo econômico
        $response = $this->actingAs($user)->delete(route('grupo_economicos.destroy', $grupo->id));
        
        // Verificar se a exclusão foi bloqueada devido às restrições de chave estrangeira
        $response->assertStatus(302); // Redirecionamento com mensagem de erro
        $this->assertDatabaseHas('grupo_economicos', ['id' => $grupo->id]);
    }

    /**
     * Testa a atualização de um colaborador para uma unidade inativa.
     *
     * @return void
     */
    public function test_atualizar_colaborador_para_unidade_inativa()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_colaborador', 'view_colaborador']);
        
        $unidadeAtiva = Unidade::factory()->create(['ativo' => true]);
        $unidadeInativa = Unidade::factory()->create(['ativo' => false]);
        
        $colaborador = Colaborador::factory()->create(['unidade_id' => $unidadeAtiva->id]);
        
        // Tentar atualizar para unidade inativa
        $response = $this->actingAs($user)->put(route('colaboradores.update', $colaborador->id), [
            'nome' => $colaborador->nome,
            'email' => $colaborador->email,
            'cpf' => $colaborador->cpf,
            'unidade_id' => $unidadeInativa->id
        ]);
        
        $response->assertSessionHasErrors(['unidade_id']);
    }

    /**
     * Testa a busca de colaboradores com filtros complexos.
     *
     * @return void
     */
    public function test_busca_avancada_colaboradores()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_colaborador');
        
        // Criar dados de teste
        $grupo1 = GrupoEconomico::factory()->create(['nome' => 'Grupo A']);
        $grupo2 = GrupoEconomico::factory()->create(['nome' => 'Grupo B']);
        
        $bandeira1 = Bandeira::factory()->create(['grupo_economico_id' => $grupo1->id, 'nome' => 'Bandeira X']);
        $bandeira2 = Bandeira::factory()->create(['grupo_economico_id' => $grupo2->id, 'nome' => 'Bandeira Y']);
        
        $unidade1 = Unidade::factory()->create(['bandeira_id' => $bandeira1->id, 'nome_fantasia' => 'Unidade Alpha']);
        $unidade2 = Unidade::factory()->create(['bandeira_id' => $bandeira2->id, 'nome_fantasia' => 'Unidade Beta']);
        
        // Colaboradores para testes
        $colaborador1 = Colaborador::factory()->create([
            'nome' => 'João Silva',
            'email' => 'joao.silva@example.com',
            'cpf' => '12345678901',
            'unidade_id' => $unidade1->id
        ]);
        
        $colaborador2 = Colaborador::factory()->create([
            'nome' => 'Maria Oliveira',
            'email' => 'maria.oliveira@example.com',
            'cpf' => '98765432109',
            'unidade_id' => $unidade2->id
        ]);
        
        // Testar filtro por grupo econômico
        $response = $this->actingAs($user)->get(route('colaboradores.index', [
            'grupo_economico_id' => $grupo1->id
        ]));
        
        $response->assertStatus(200);
        $response->assertSee($colaborador1->nome);
        $response->assertDontSee($colaborador2->nome);
        
        // Testar filtro por bandeira
        $response = $this->actingAs($user)->get(route('colaboradores.index', [
            'bandeira_id' => $bandeira2->id
        ]));
        
        $response->assertStatus(200);
        $response->assertDontSee($colaborador1->nome);
        $response->assertSee($colaborador2->nome);
        
        // Testar filtro por unidade
        $response = $this->actingAs($user)->get(route('colaboradores.index', [
            'unidade_id' => $unidade1->id
        ]));
        
        $response->assertStatus(200);
        $response->assertSee($colaborador1->nome);
        $response->assertDontSee($colaborador2->nome);
    }

    /**
     * Testa a paginação de resultados.
     *
     * @return void
     */
    public function test_paginacao_de_resultados()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_colaborador');
        
        // Criar 30 colaboradores para testar paginação
        $unidade = Unidade::factory()->create();
        Colaborador::factory()->count(30)->create(['unidade_id' => $unidade->id]);
        
        // A página padrão deve mostrar 15 itens (configuração padrão do Laravel)
        $response = $this->actingAs($user)->get(route('colaboradores.index'));
        $response->assertStatus(200);
        $response->assertViewHas('colaboradores', function ($colaboradores) {
            return $colaboradores->count() === 15;
        });
        
        // Verificar links de paginação
        $response->assertSee('pagination');
        
        // Acessar a segunda página
        $response = $this->actingAs($user)->get(route('colaboradores.index', ['page' => 2]));
        $response->assertStatus(200);
        $response->assertViewHas('colaboradores', function ($colaboradores) {
            return $colaboradores->count() === 15;
        });
    }

    /**
     * Testa a ordenação de resultados.
     *
     * @return void
     */
    public function test_ordenacao_de_resultados()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_colaborador');
        
        // Criar colaboradores com nomes em ordem aleatória
        $unidade = Unidade::factory()->create();
        Colaborador::factory()->create(['nome' => 'Carlos', 'unidade_id' => $unidade->id]);
        Colaborador::factory()->create(['nome' => 'Ana', 'unidade_id' => $unidade->id]);
        Colaborador::factory()->create(['nome' => 'Bruno', 'unidade_id' => $unidade->id]);
        
        // Ordenar por nome em ordem crescente
        $response = $this->actingAs($user)->get(route('colaboradores.index', [
            'sort_by' => 'nome',
            'sort_order' => 'asc'
        ]));
        
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Ana', 'Bruno', 'Carlos']);
        
        // Ordenar por nome em ordem decrescente
        $response = $this->actingAs($user)->get(route('colaboradores.index', [
            'sort_by' => 'nome',
            'sort_order' => 'desc'
        ]));
        
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Carlos', 'Bruno', 'Ana']);
    }
}
