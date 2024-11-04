<aside class="w-64 bg-[#10163f] text-white min-h-screen">
    <div class="p-4">
        <h1 class="text-2xl font-bold text-[#ffd200] mb-4">Disckatus</h1>
        <nav>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center p-2 {{ request()->routeIs('dashboard') ? 'bg-[#ffd200] text-[#10163f]' : 'hover:bg-[#ffd200] hover:text-[#10163f]' }} rounded transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>

                @if(auth()->user()->hasAnyRole(['super-admin', 'captain']))
                    <li>
                        <a href="{{ route('members.index') }}" 
                           class="flex items-center p-2 {{ request()->routeIs('members.*') ? 'bg-[#ffd200] text-[#10163f]' : 'hover:bg-[#ffd200] hover:text-[#10163f]' }} rounded transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Miembros
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center p-2 {{ request()->routeIs('profile.*') ? 'bg-[#ffd200] text-[#10163f]' : 'hover:bg-[#ffd200] hover:text-[#10163f]' }} rounded transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Mi Perfil
                    </a>
                </li>

                @if(auth()->user()->hasRole('super-admin'))
                    <li>
                        <a href="{{ route('admin.roles.index') }}" 
                           class="flex items-center p-2 {{ request()->routeIs('admin.roles.*') ? 'bg-[#ffd200] text-[#10163f]' : 'hover:bg-[#ffd200] hover:text-[#10163f]' }} rounded transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Gestión de Roles
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    <!-- Información del Usuario -->
    <div class="absolute bottom-0 w-64 p-4 border-t border-gray-700">
        <div class="flex items-center mb-4">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" 
                     alt="Avatar" 
                     class="w-10 h-10 rounded-full mr-3">
            @else
                <div class="w-10 h-10 rounded-full bg-[#ffd200] text-[#10163f] flex items-center justify-center font-bold mr-3">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
            <div>
                <p class="font-medium">{{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-300">{{ auth()->user()->getHighestRole()?->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" 
                    class="flex items-center p-2 w-full hover:bg-[#ffd200] hover:text-[#10163f] rounded transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>