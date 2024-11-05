<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-[#10163f]">Registro en Disckatus</h2>
                <p class="text-gray-600">Crea tu cuenta para empezar</p>
            </div>

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Contraseña')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('login') }}">
                        {{ __('¿Ya tienes cuenta?') }}
                    </a>

                    <x-primary-button class="ml-4">
                        {{ __('Registrarse') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Separator -->
            <div class="mt-6 flex items-center justify-between">
                <div class="border-t border-gray-300 flex-grow mr-3"></div>
                <span class="text-gray-500 text-sm">O regístrate con</span>
                <div class="border-t border-gray-300 flex-grow ml-3"></div>
            </div>

            <!-- Social Register -->
            <div class="mt-6">
                <button onclick="signInWithGoogle()" type="button"
                    class="w-full flex items-center justify-center gap-3 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#10163f]">
                    <img class="h-5 w-5" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                    <span>Continuar con Google</span>
                </button>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="module">
            import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
            import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-auth.js";

            // Configuración directa de Firebase (temporal para pruebas)
            const firebaseConfig = {
                apiKey: "{{ env('FIREBASE_API_KEY') }}",
                authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
                projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
                storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
                messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
                appId: "{{ env('FIREBASE_APP_ID') }}",
                measurementId: "{{ env('FIREBASE_MEASUREMENT_ID') }}"
            };

            // Initialize Firebase
            const app = initializeApp(firebaseConfig);
            const auth = getAuth(app);
            const provider = new GoogleAuthProvider();

            // Función global para el login con Google
            window.signInWithGoogle = async function () {
                try {
                    const result = await signInWithPopup(auth, provider);
                    const idToken = await result.user.getIdToken();

                    // Enviar token al backend
                    const response = await fetch('/api/auth/google/callback', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ idToken })
                    });

                    const data = await response.json();
                    if (data.status === 'success') {
                        window.location.href = data.redirect;
                    } else {
                        throw new Error(data.message || 'Error en la autenticación');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    // Mostrar error en la UI
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4';
                    errorDiv.textContent = 'Error al iniciar sesión con Google: ' + error.message;
                    document.querySelector('form').appendChild(errorDiv);
                }
            };
        </script>
    @endpush
</x-guest-layout>