<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-[#10163f]">Disckatus</h2>
                <p class="text-gray-600">Gestión de equipo deportivo</p>
            </div>

            <!-- Formulario de login -->
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-[#10163f] shadow-sm focus:border-[#10163f] focus:ring focus:ring-[#10163f] focus:ring-opacity-50" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                    </label>

                    <a class="text-sm text-[#10163f] hover:text-[#ffd200]" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                </div>

                <x-primary-button class="w-full justify-center">
                    {{ __('Iniciar sesión') }}
                </x-primary-button>
            </form>

            <!-- Separador -->
            <div class="mt-6 flex items-center justify-between">
                <div class="border-t border-gray-300 flex-grow mr-3"></div>
                <span class="text-gray-500 text-sm">O continúa con</span>
                <div class="border-t border-gray-300 flex-grow ml-3"></div>
            </div>

            <!-- Botón de Google -->
            <div class="mt-6">
                <button onclick="signInWithGoogle()" type="button" class="w-full flex items-center justify-center gap-3 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#10163f]">
                    <img class="h-5 w-5" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                    <span>Continuar con Google</span>
                </button>
            </div>

            <p class="mt-6 text-center text-sm text-gray-600">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="font-medium text-[#10163f] hover:text-[#ffd200]">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
    <script type="module">
        // Importaciones de Firebase
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        console.log('Firebase Config:', firebaseConfig);

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        window.signInWithGoogle = async function() {
            try {
                const result = await signInWithPopup(auth, provider);
                console.log('Google sign in successful:', result);
                
                const idToken = await result.user.getIdToken();
                console.log('Token obtenido successfully');

                const response = await fetch('/api/auth/google/callback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ idToken })
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server response:', text);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Backend response:', data);

                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Error en la autenticación');
                }
            } catch (error) {
                console.error('Error completo:', error);
                alert(`Error al iniciar sesión con Google: ${error.message}`);
            }
        };
    </script>
    @endpush
</x-guest-layout>