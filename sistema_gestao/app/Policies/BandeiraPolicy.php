<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bandeira;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandeiraPolicy
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
        return $user->can('view_bandeira');
    }

    /**
     * Determina se o usuário pode visualizar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bandeira  $bandeira
     * @return bool
     */
    public function view(User $user, Bandeira $bandeira): bool
    {
        return $user->can('view_bandeira');
    }

    /**
     * Determina se o usuário pode criar modelos.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_bandeira');
    }

    /**
     * Determina se o usuário pode atualizar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bandeira  $bandeira
     * @return bool
     */
    public function update(User $user, Bandeira $bandeira): bool
    {
        return $user->can('edit_bandeira');
    }

    /**
     * Determina se o usuário pode excluir o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bandeira  $bandeira
     * @return bool
     */
    public function delete(User $user, Bandeira $bandeira): bool
    {
        return $user->can('delete_bandeira');
    }

    /**
     * Determina se o usuário pode restaurar o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bandeira  $bandeira
     * @return bool
     */
    public function restore(User $user, Bandeira $bandeira): bool
    {
        return $user->can('delete_bandeira');
    }

    /**
     * Determina se o usuário pode excluir permanentemente o modelo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bandeira  $bandeira
     * @return bool
     */
    public function forceDelete(User $user, Bandeira $bandeira): bool
    {
        return $user->can('force_delete_bandeira');
    }
}
