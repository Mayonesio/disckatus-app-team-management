<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(auth()->user()->isAdmin())
                        <h3 class="text-lg font-bold mb-4">Panel de Administrador</h3>
                        <!-- Contenido específico para admin -->
                    @elseif(auth()->user()->isCaptain())
                        <h3 class="text-lg font-bold mb-4">Panel de Capitán</h3>
                        <!-- Contenido específico para capitán -->
                    @else
                        <h3 class="text-lg font-bold mb-4">Panel de Jugador</h3>
                        <!-- Contenido general para jugadores -->
                    @endif

                    <div class="mt-4">
                        <!-- Información del usuario -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold">Tu perfil:</h4>
                            <p>Nombre: {{ auth()->user()->name }}</p>
                            <p>Email: {{ auth()->user()->email }}</p>
                            <p>Rol: {{ auth()->user()->getHighestRole()?->name ?? 'Sin rol asignado' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>