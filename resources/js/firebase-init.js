import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-auth.js";

let app;
let auth;

export function initFirebase(config) {
    if (!app) {
        app = initializeApp(config);
        auth = getAuth(app);
    }
    return { app, auth };
}

export function getFirebaseAuth() {
    return auth;
}

export async function getFirebaseToken() {
    try {
        const user = auth?.currentUser;
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
}