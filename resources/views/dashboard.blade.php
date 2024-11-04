@extends('layouts.app')

@section('title', 'Dashboard')
@section('header')
<div class="flex items-center justify-between">
    @section('sidebar')
    @include('components.sidebar')
@endsection
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">Bienvenido, {{ auth()->user()->name }}</h1>
        <span class="ml-2 px-2 py-1 text-sm rounded-full 
                     {{ auth()->user()->getHighestRole()->slug === 'super-admin' ? 'bg-purple-100 text-purple-800' :
                        (auth()->user()->getHighestRole()->slug === 'captain' ? 'bg-blue-100 text-blue-800' :
                        (auth()->user()->getHighestRole()->slug === 'sotg-captain' ? 'bg-green-100 text-green-800' :
                         'bg-gray-100 text-gray-800')) }}">
            {{ auth()->user()->getHighestRole()->name }}
        </span>
    </div>
    
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 bg-[#10163f] text-white rounded-lg hover:bg-[#ffd200] hover:text-[#10163f]">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Panel de Administración
        </a>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Vista general para todos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Miembros card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Miembros</h2>
            <p class="text-4xl font-bold text-[#10163f]">{{ $totalMembers ?? 0 }}</p>
            <p class="text-sm text-gray-500">{{ $newMembersThisMonth ?? 0 }} nuevos este mes</p>
        </div>

        <!-- Torneos card -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Próximos Torneos</h2>
            <ul class="space-y-2">
                @if(isset($upcomingTournaments) && count($upcomingTournaments) > 0)
                    @foreach($upcomingTournaments as $tournament)
                        <li class="flex justify-between items-center">
                            <span>{{ $tournament->name }}</span>
                            <span class="text-sm text-gray-500">{{ $tournament->date->format('d M') }}</span>
                        </li>
                    @endforeach
                @else
                    <li class="text-gray-500">No hay torneos próximos</li>
                @endif
            </ul>
        </div>

        <!-- Pagos card - Solo visible para admin y captain -->
        @if(auth()->user()->hasAnyRole(['super-admin', 'captain']))
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Estado de Pagos</h2>
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-full border-4 border-[#ffd200] flex items-center justify-center">
                        <span class="text-xl font-bold">{{ $paymentPercentage ?? 0 }}%</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pagos al día</p>
                        <p class="text-sm text-red-500">{{ $overduePayments ?? 0 }} morosos</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sección SOTG - Solo visible para admin y sotg-captain -->
    @if(auth()->user()->hasAnyRole(['super-admin', 'sotg-captain']))
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Spirit of the Game</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2">Última Evaluación</h3>
                    <p class="text-3xl font-bold text-green-600">8.5</p>
                    <p class="text-sm text-gray-500">Torneo Madrid Open</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2">Próxima Reunión SOTG</h3>
                    <p class="text-gray-700">Viernes, 18:00</p>
                    <p class="text-sm text-gray-500">Revisión de evaluaciones</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Próximos entrenamientos -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Próximos Entrenamientos</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 border rounded-lg">
                <h3 class="font-medium">Lunes</h3>
                <p class="text-gray-600">19:00 - 21:00</p>
                <p class="text-sm text-gray-500">Campo Municipal</p>
            </div>
            <div class="p-4 border rounded-lg">
                <h3 class="font-medium">Miércoles</h3>
                <p class="text-gray-600">19:00 - 21:00</p>
                <p class="text-sm text-gray-500">Campo Municipal</p>
            </div>
        </div>
    </div>

    <!-- Panel de Acciones Rápidas - Varía según el rol -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(auth()->user()->hasAnyRole(['super-admin', 'captain']))
            <a href="{{ route('members.create') }}" 
               class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <h3 class="font-medium text-[#10163f]">Añadir Miembro</h3>
                <p class="text-sm text-gray-500">Registrar nuevo jugador</p>
            </a>
            <a href="{{ route('tournaments.create') }}" 
               class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <h3 class="font-medium text-[#10163f]">Crear Torneo</h3>
                <p class="text-sm text-gray-500">Programar nuevo torneo</p>
            </a>
        @endif
        
        @if(auth()->user()->hasRole('sotg-captain'))
            <a href="{{ route('sotg.evaluate') }}" 
               class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <h3 class="font-medium text-[#10163f]">Evaluar SOTG</h3>
                <p class="text-sm text-gray-500">Nueva evaluación de espíritu</p>
            </a>
        @endif

        <a href="{{ route('profile.edit') }}" 
           class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <h3 class="font-medium text-[#10163f]">Mi Perfil</h3>
            <p class="text-sm text-gray-500">Ver y editar información</p>
        </a>
    </div>
</div>
@endsection