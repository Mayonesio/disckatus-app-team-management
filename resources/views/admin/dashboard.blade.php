@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('header')
<h1 class="text-2xl font-bold">Panel de Administración</h1>
@endsection

@section('sidebar')
    @include('components.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Estadísticas Generales -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Usuarios</h3>
            <div class="text-3xl font-bold text-[#10163f]">{{ $totalUsers ?? 0 }}</div>
            <div class="text-sm text-gray-500">{{ $newUsers ?? 0 }} nuevos este mes</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Roles</h3>
            <div class="space-y-2">
                @foreach($roleStats ?? [] as $role)
                    <div class="flex justify-between items-center">
                        <span>{{ $role['name'] }}</span>
                        <span class="px-2 py-1 bg-gray-100 rounded-full text-sm">
                            {{ $role['count'] }} usuarios
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Sistema</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Versión PHP:</span>
                    <span class="font-medium">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Laravel:</span>
                    <span class="font-medium">{{ app()->version() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users') }}" 
               class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h4 class="font-medium text-[#10163f]">Gestionar Usuarios</h4>
                <p class="text-sm text-gray-500">Administrar usuarios y roles</p>
            </a>
            
            <a href="{{ route('admin.roles.index') }}" 
               class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h4 class="font-medium text-[#10163f]">Gestionar Roles</h4>
                <p class="text-sm text-gray-500">Configurar roles y permisos</p>
            </a>

            <a href="{{ route('admin.system') }}" 
               class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <h4 class="font-medium text-[#10163f]">Configuración</h4>
                <p class="text-sm text-gray-500">Ajustes del sistema</p>
            </a>
        </div>
    </div>
</div>
@endsection