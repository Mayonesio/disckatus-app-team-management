<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use App\Models\PlayerProfile;

class TestController extends Controller
{
    public function checkDatabase()
    {
        return response()->json([
            'database_status' => 'connected',
            'tables' => [
                'roles' => [
                    'count' => Role::count(),
                    'data' => Role::all()
                ],
                'skills' => [
                    'count' => Skill::count(),
                    'data' => Skill::all()
                ],
                'users' => [
                    'count' => User::count()
                ],
                'player_profiles' => [
                    'count' => PlayerProfile::count()
                ]
            ]
        ]);
    }
}