<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Desativar verificações de chave estrangeira para evitar erros
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Limpar tabelas
        $tables = [
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'role_has_permissions',
            'model_has_permissions'
        ];
        
        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
        }
        
        // Reativar verificações de chave estrangeira
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Chamar os seeders
        $this->call([
            PermissionSeeder::class,
        ]);
    }
}
