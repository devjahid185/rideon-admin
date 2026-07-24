<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;

class FirebaseAuthService
{
    protected $auth;

    public function __construct()
    {
        $keyFile = json_decode(file_get_contents(
            storage_path('firebase/firebase_credentials.json')
        ), true);

        $this->db = new FirestoreClient([
            'keyFile' => $keyFile, // 👈 load directly, avoids recursion
            'projectId' => 'dummy-b0665',
            'transport' => 'rest',               // 👈 safer than gRPC for PHP 8.3
            'apiEndpoint' => 'firestore.googleapis.com',
        ]);
    }

    /**
     * Create a Firebase Custom Token using UID and optional custom claims
     */
    public function createCustomToken(string $uid, array $claims = []): string
    {
        return $this->auth->createCustomToken($uid, $claims)->toString();
    }
}
