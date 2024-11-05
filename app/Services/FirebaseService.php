<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private FirebaseAuth $auth;

    public function __construct()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
                
            $this->auth = $factory->createAuth();
            Log::info('Firebase Auth inicializado correctamente');
        } catch (\Exception $e) {
            Log::error('Error inicializando Firebase Auth', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getAuth(): FirebaseAuth
    {
        return $this->auth;
    }

    public function verifyToken(string $idToken)
    {
        try {
            Log::info('Intentando verificar token Firebase');
            $verifiedToken = $this->auth->verifyIdToken($idToken);
            Log::info('Token Firebase verificado correctamente');
            return $verifiedToken;
        } catch (\Exception $e) {
            Log::error('Error verificando token Firebase', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getUser(string $uid)
    {
        try {
            return $this->auth->getUser($uid);
        } catch (\Exception $e) {
            Log::error('Error obteniendo usuario por UID', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getUserByEmail(string $email)
    {
        try {
            return $this->auth->getUserByEmail($email);
        } catch (\Exception $e) {
            Log::error('Error obteniendo usuario por email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}