<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PlayerProfile;
use Illuminate\Http\Request;

class CaptainController extends Controller
{
    public function dashboard()
    {
        $teamStats = $this->getTeamStats();
        return view('captain.dashboard', compact('teamStats'));
    }

    public function team()
    {
        $players = User::whereHas('roles', function($q) {
            $q->where('slug', 'player');
        })->with('playerProfile')->get();

        return view('captain.team', compact('players'));
    }

    public function stats()
    {
        $stats = [
            'total_players' => User::whereHas('roles', function($q) {
                $q->where('slug', 'player');
            })->count(),
            'active_players' => PlayerProfile::where('active', true)->count(),
            // Añade más estadísticas según necesites
        ];

        return view('captain.stats', compact('stats'));
    }

    protected function getTeamStats()
    {
        return [
            'total_members' => User::whereHas('roles', function($q) {
                $q->whereIn('slug', ['player', 'captain']);
            })->count(),
            'practice_attendance' => 0, // Implementar lógica de asistencia
            'upcoming_events' => [] // Implementar lógica de eventos
        ];
    }
}