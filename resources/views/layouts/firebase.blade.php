<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <title>{{ config('app.name', 'Disckatus') }}</title>

    <!-- Scripts y Styles originales -->
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
                console.log('Firebase token actualizado');
            }
        });
    </script>

    @yield('head')
</head>
<body>
    @yield('body')
</body>
</html>