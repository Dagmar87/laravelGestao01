<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Executa o seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões para Grupos Econômicos
        Permission::create(['name' => 'view_grupo_economico']);
        Permission::create(['name' => 'create_grupo_economico']);
        Permission::create(['name' => 'edit_grupo_economico']);
        Permission::create(['name' => 'delete_grupo_economico']);

        // Criar permissões para Bandeiras
        Permission::create(['name' => 'view_bandeira']);
        Permission::create(['name' => 'create_bandeira']);
        Permission::create(['name' => 'edit_bandeira']);
        Permission::create(['name' => 'delete_bandeira']);

        // Criar permissões para Unidades
        Permission::create(['name' => 'view_unidade']);
        Permission::create(['name' => 'create_unidade']);
        Permission::create(['name' => 'edit_unidade']);
        Permission::create(['name' => 'delete_unidade']);

        // Criar permissões para Colaboradores
        Permission::create(['name' => 'view_colaborador']);
        Permission::create(['name' => 'create_colaborador']);
        Permission::create(['name' => 'edit_colaborador']);
        Permission::create(['name' => 'delete_colaborador']);

        // Criar papéis
        $adminRole = Role::create(['name' => 'admin']);
        $gerenteRole = Role::create(['name' => 'gerente']);
        $usuarioRole = Role::create(['name' => 'usuario']);

        // Atribuir todas as permissões ao papel de admin
        $adminRole->givePermissionTo(Permission::all());

        // Atribuir permissões específicas ao papel de gerente
        $gerenteRole->givePermissionTo([
            'view_grupo_economico',
            'view_bandeira', 'create_bandeira', 'edit_bandeira',
            'view_unidade', 'create_unidade', 'edit_unidade',
            'view_colaborador', 'create_colaborador', 'edit_colaborador', 'delete_colaborador'
        ]);

        // Atribuir permissões básicas ao papel de usuário
        $usuarioRole->givePermissionTo([
            'view_grupo_economico',
            'view_bandeira',
            'view_unidade',
            'view_colaborador'
        ]);

        // Criar usuário administrador
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $adminUser->assignRole('admin');

        // Criar usuário gerente
        $gerenteUser = User::create([
            'name' => 'Gerente',
            'email' => 'gerente@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $gerenteUser->assignRole('gerente');

        // Criar usuário comum
        $usuarioUser = User::create([
            'name' => 'Usuário Comum',
            'email' => 'usuario@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $usuarioUser->assignRole('usuario');
    }
}
