<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;

class FirestoreService
{
    protected $db;

    public function __construct()
    {
        $keyFile = json_decode(file_get_contents(
            storage_path('firebase/firebase_credentials.json')
        ), true);

        $this->db = new FirestoreClient([
            'keyFile' => $keyFile,
            'projectId' => 'dummy-b0665',
            'transport' => 'rest',
            'apiEndpoint' => 'firestore.googleapis.com',
        ]);

    }

    public function getCollection(string $collection)
    {
        return $this->db->collection($collection)->documents();
    }

    public function addDocument(string $collection, array $data)
    {
        $maxAttempts = 3;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $attempt++;

                return $this->db->collection($collection)->add($data);
            } catch (\Throwable $e) {
                if ($attempt >= $maxAttempts) {
                    throw $e;
                }
                \Log::warning("Firestore addDocument attempt {$attempt} failed: ".$e->getMessage());
                usleep(pow(2, $attempt) * 200000);
            }
        }
    }

    public function updateDocument(string $collection, string $documentId, array $data): void
    {
        $maxAttempts = 3;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $attempt++;
                $this->db->collection($collection)
                    ->document($documentId)
                    ->update(
                        array_map(fn ($k, $v) => ['path' => $k, 'value' => $v], array_keys($data), $data)
                    );

                return;
            } catch (\Throwable $e) {
                \Log::warning("Firestore updateDocument attempt {$attempt} failed for document {$documentId}: ".$e->getMessage());
                if ($attempt >= $maxAttempts) {
                    \Log::error("Firestore updateDocument failed after {$attempt} attempts for document {$documentId}");
                    throw $e;
                }
                usleep(pow(2, $attempt) * 200000);
            }
        }
    }

    public function deleteDocument(string $collection, string $documentId): void
    {
        $maxAttempts = 3;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $attempt++;
                $this->db->collection($collection)
                    ->document($documentId)
                    ->delete();

                return;
            } catch (\Throwable $e) {
                \Log::warning("Firestore deleteDocument attempt {$attempt} failed for document {$documentId}: ".$e->getMessage());
                if ($attempt >= $maxAttempts) {
                    \Log::error("Firestore deleteDocument failed after {$attempt} attempts for document {$documentId}");
                    throw $e;
                }
                usleep(pow(2, $attempt) * 200000);
            }
        }
    }

    public function getDocument(string $collection, string $documentId): ?array
    {
        $snapshot = $this->db->collection($collection)->document($documentId)->snapshot();

        if (! $snapshot->exists()) {
            return null;
        }

        return $snapshot->data();
    }

    public function getDb()
    {
        return $this->db;
    }
}
