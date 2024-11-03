<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Administrador con acceso total al sistema'
            ],
            [
                'name' => 'Capitán',
                'slug' => 'captain',
                'description' => 'Capitán del equipo'
            ],
            [
                'name' => 'Capitán SOTG',
                'slug' => 'sotg-captain',
                'description' => 'Capitán de Espíritu de Juego'
            ],
            [
                'name' => 'Tesorero',
                'slug' => 'treasurer',
                'description' => 'Encargado de finanzas'
            ],
            [
                'name' => 'Jugador',
                'slug' => 'player',
                'description' => 'Jugador del equipo'
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']], // Buscar por slug
                $role // Datos a actualizar/crear
            );
        }
    }
}