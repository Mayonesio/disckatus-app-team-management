<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $newUsers = User::whereMonth('created_at', now()->month)->count();

        $roleStats = Role::withCount('users')
            ->get()
            ->map(function ($role) {
                return [
                    'name' => $role->name,
                    'count' => $role->users_count
                ];
            });

        return view('admin.dashboard', compact('totalUsers', 'newUsers', 'roleStats'));
    }

    public function users()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();
        return view('admin.users', compact('users', 'roles'));
    }

    
    // En AdminController.php añadir:
    public function createSuperAdmin(Request $request)
    {
        // Este método puede manejar ambos casos (Firebase y tradicional)
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
    
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $superAdminRole = Role::where('slug', 'super-admin')->firstOrFail();
            
            $user->roles()->sync([$superAdminRole->id]);
    
            return response()->json([
                'success' => true,
                'message' => 'Super Admin creado correctamente',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando super admin: ' . $e->getMessage());
            return response()->json(['error' => 'Error creando super admin'], 500);
        }
    }

    public function system()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'database_connection' => config('database.default'),
        ];

        return view('admin.system', compact('systemInfo'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            $user->roles()->sync([$request->role_id]);
            return back()->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            Log::error('Error actualizando rol: ' . $e->getMessage());
            return back()->with('error', 'Error actualizando rol');
        }
    }
}