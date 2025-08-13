<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Colaborador;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColaboradorPolicy
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
        return $user->can('view_colaborador');
    }

    /**
     * Determina se o usuário pode visualizar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Colaborador  $colaborador
     * @return bool
     */
    public function view(User $user, Colaborador $colaborador): bool
    {
        return $user->can('view_colaborador');
    }

    /**
     * Determina se o usuário pode criar modelos.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_colaborador');
    }

    /**
     * Determina se o usuário pode atualizar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Colaborador  $colaborador
     * @return bool
     */
    public function update(User $user, Colaborador $colaborador): bool
    {
        return $user->can('edit_colaborador');
    }

    /**
     * Determina se o usuário pode excluir o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Colaborador  $colaborador
     * @return bool
     */
    public function delete(User $user, Colaborador $colaborador): bool
    {
        return $user->can('delete_colaborador');
    }

    /**
     * Determina se o usuário pode restaurar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Colaborador  $colaborador
     * @return bool
     */
    public function restore(User $user, Colaborador $colaborador): bool
    {
        return $user->can('delete_colaborador');
    }

    /**
     * Determina se o usuário pode excluir permanentemente o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Colaborador  $colaborador
     * @return bool
     */
    public function forceDelete(User $user, Colaborador $colaborador): bool
    {
        return $user->can('force_delete_colaborador');
    }
}
