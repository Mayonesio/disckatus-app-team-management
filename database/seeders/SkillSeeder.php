<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            [
                'name' => 'Backhand',
                'description' => 'Lanzamiento básico de revés'
            ],
            [
                'name' => 'Forehand',
                'description' => 'Lanzamiento básico de derecha'
            ],
            [
                'name' => 'Hammer',
                'description' => 'Lanzamiento sobre la cabeza'
            ],
            [
                'name' => 'Scoober',
                'description' => 'Lanzamiento especial sobre la marca'
            ]
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['name' => $skill['name']], // Buscar por nombre
                $skill // Datos a actualizar/crear
            );
        }
    }
}