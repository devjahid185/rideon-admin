<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\File;

class FirebaseAuthService
{
    protected array $credentials;

    public function __construct()
    {
        $credentialsPath = storage_path('firebase/firebase_credentials.json');

        if (! File::exists($credentialsPath)) {
            throw new \Exception("Firebase credentials not found at: $credentialsPath");
        }

        $this->credentials = json_decode(File::get($credentialsPath), true);
    }

    /**
     * Create a Firebase Custom Token using UID and optional custom claims
     */
    public function createCustomToken(string $uid, array $claims = []): string
    {
        $now = time();
        $privateKey = $this->credentials['private_key'];
        $clientEmail = $this->credentials['client_email'];

        $payload = [
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => $now,
            'exp' => $now + (60 * 60), // 1 hour expiration
            'uid' => $uid,
            'claims' => $claims,
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }
}
