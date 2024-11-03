<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-[#10163f]">Recuperar Contraseña</h2>
                <p class="text-gray-600">Te enviaremos un enlace para recuperar tu contraseña</p>
            </div>

            <!-- Session Status -->
            <div class="mb-4 text-sm text-gray-600">
                {{ __('¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu dirección de email y te enviaremos un enlace para que puedas crear una nueva contraseña.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Volver al login') }}
                    </a>

                    <x-primary-button>
                        {{ __('Enviar enlace') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>