<?php

namespace App\Providers;

use App\Models\Bandeira;
use App\Models\Colaborador;
use App\Models\GrupoEconomico;
use App\Models\Unidade;
use App\Policies\BandeiraPolicy;
use App\Policies\ColaboradorPolicy;
use App\Policies\GrupoEconomicoPolicy;
use App\Policies\UnidadePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * As políticas do modelo para o aplicativo.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        GrupoEconomico::class => GrupoEconomicoPolicy::class,
        Bandeira::class => BandeiraPolicy::class,
        Unidade::class => UnidadePolicy::class,
        Colaborador::class => ColaboradorPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }

    /**
     * Registra as políticas de autorização do aplicativo.
     *
     * @return void
     */
    public function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
