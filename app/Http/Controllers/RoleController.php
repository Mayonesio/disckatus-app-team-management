<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string'
        ]);

        try {
            Role::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description
            ]);

            return back()->with('success', 'Rol creado correctamente');
        } catch (\Exception $e) {
            Log::error('Error creando rol: ' . $e->getMessage());
            return back()->with('error', 'Error al crear el rol');
        }
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string'
        ]);

        try {
            $role->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description
            ]);

            return back()->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            Log::error('Error actualizando rol: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el rol');
        }
    }
}