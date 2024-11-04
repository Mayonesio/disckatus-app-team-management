<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function index()
    {
        $players = User::whereHas('roles', function($query) {
            $query->where('slug', 'player');
        })->with('playerProfile')->get();

        return view('members.index', compact('players'));
    }

    public function show(User $user)
    {
        $user->load('playerProfile', 'roles');
        return view('members.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('playerProfile');
        return view('members.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'player_profile.number' => 'nullable|integer',
            'player_profile.position' => 'nullable|string|max:50',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->playerProfile) {
            $user->playerProfile->update([
                'number' => $validated['player_profile']['number'] ?? null,
                'position' => $validated['player_profile']['position'] ?? null,
            ]);
        }

        return redirect()->route('members.show', $user)
            ->with('status', 'Perfil actualizado correctamente');
    }
}