<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GrupoEconomico;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrupoEconomicoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar qualquer modelo.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_grupo_economico');
    }

    /**
     * Determina se o usuário pode visualizar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return bool
     */
    public function view(User $user, GrupoEconomico $grupoEconomico): bool
    {
        return $user->can('view_grupo_economico');
    }

    /**
     * Determina se o usuário pode criar modelos.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_grupo_economico');
    }

    /**
     * Determina se o usuário pode atualizar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return bool
     */
    public function update(User $user, GrupoEconomico $grupoEconomico): bool
    {
        return $user->can('edit_grupo_economico');
    }

    /**
     * Determina se o usuário pode excluir o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return bool
     */
    public function delete(User $user, GrupoEconomico $grupoEconomico): bool
    {
        return $user->can('delete_grupo_economico');
    }

    /**
     * Determina se o usuário pode restaurar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return bool
     */
    public function restore(User $user, GrupoEconomico $grupoEconomico): bool
    {
        return $user->can('delete_grupo_economico');
    }

    /**
     * Determina se o usuário pode excluir permanentemente o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GrupoEconomico  $grupoEconomico
     * @return bool
     */
    public function forceDelete(User $user, GrupoEconomico $grupoEconomico): bool
    {
        return $user->can('force_delete_grupo_economico');
    }
}
