<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Firebase Setup -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
        import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        // Solo inicializar Firebase si el usuario está autenticado con Google
        if ({{ auth()->check() && auth()->user()->firebase_uid ? 'true' : 'false' }}) {
            const app = initializeApp(firebaseConfig);
            const auth = getAuth(app);

            window.getFirebaseToken = async () => {
                try {
                    const user = auth.currentUser;
                    if (user) {
                        const token = await user.getIdToken(true);
                        localStorage.setItem('firebase_token', token);
                        return token;
                    }
                    return localStorage.getItem('firebase_token');
                } catch (error) {
                    console.error('Error getting Firebase token:', error);
                    return null;
                }
            };

            onAuthStateChanged(auth, async (user) => {
                if (user) {
                    const token = await user.getIdToken(true);
                    localStorage.setItem('firebase_token', token);
                }
            });
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        @yield('sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            @yield('header')
                        </div>

                        <!-- User Menu -->
                        @auth
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-4">
                                    {{ auth()->user()->name }}
                                    @if(auth()->user()->roles->isNotEmpty())
                                        ({{ auth()->user()->getHighestRole()->name }})
                                    @endif
                                </span>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="flex-1 p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>