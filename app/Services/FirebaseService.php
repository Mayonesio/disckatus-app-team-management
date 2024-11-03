<?php
// app/Services/FirebaseService.php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseService
{
    private FirebaseAuth $auth;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
            
        $this->auth = $factory->createAuth();
    }

    public function verifyToken(string $idToken)
    {
        try {
            return $this->auth->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUser(string $uid)
    {
        try {
            return $this->auth->getUser($uid);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUserByEmail(string $email)
    {
        try {
            return $this->auth->getUserByEmail($email);
        } catch (\Exception $e) {
            return null;
        }
    }
}