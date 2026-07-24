<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FirestoreService
{
    protected $projectId;

    protected $httpClient;

    protected $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = $this->resolveCredentialsPath();
        if (! $this->credentialsPath || ! file_exists($this->credentialsPath)) {
            throw new \Exception('Firebase credentials file not found for Firestore.');
        }

        $credentials = json_decode(file_get_contents($this->credentialsPath), true);
        if (! is_array($credentials)) {
            throw new \Exception('Invalid Firebase credentials JSON for Firestore.');
        }

        $this->projectId = env('FIREBASE_PROJECT_ID') ?: ($credentials['project_id'] ?? null);
        if (! $this->projectId) {
            throw new \Exception('Firebase project_id missing for Firestore.');
        }

        $googleClient = new Google_Client;
        $googleClient->setAuthConfig($this->credentialsPath);
        $googleClient->addScope('https://www.googleapis.com/auth/datastore');

        $accessToken = $googleClient->fetchAccessTokenWithAssertion()['access_token'] ?? null;

        if (! $accessToken) {
            throw new \Exception('Failed to retrieve Google access token.');
        }

        $this->httpClient = new HttpClient([
            'base_uri' => "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/",
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function resolveCredentialsPath(): ?string
    {
        $envPath = env('FIREBASE_CREDENTIALS_JSON_PATH');
        $candidates = array_filter([
            $envPath,
            storage_path('firebase/firebase_credentials.json'),
            storage_path('app/firebase/firebase_credentials.json'),
            base_path('storage/firebase/firebase_credentials.json'),
            base_path('../storage/firebase/firebase_credentials.json'),
            base_path('public_html/storage/firebase/firebase_credentials.json'),
            base_path('../public_html/storage/firebase/firebase_credentials.json'),
        ]);

        foreach ($candidates as $path) {
            if (is_string($path) && file_exists($path)) {
                return $path;
            }
        }

        Log::error('Firestore credentials file not found', [
            'candidates' => $candidates,
        ]);

        return null;
    }

    public function addDocument($collection, $data)
    {
        try {
            $response = $this->httpClient->post($collection, [
                'json' => ['fields' => $this->transformData($data)],
            ]);

            $body = json_decode($response->getBody(), true);

            return $body['name'] ?? null;
        } catch (RequestException $e) {
            \Log::error('Firestore addDocument failed: '.$e->getMessage(), [
                'collection' => $collection,
                'data' => $data,
            ]);

            return null;
        }
    }

    public function updateDocument(string $collection, string $documentId, array $data): ?array
    {
        if (empty($data)) {
            \Log::warning('Firestore updateDocument called with empty data', [
                'collection' => $collection,
                'documentId' => $documentId,
            ]);
            throw new \InvalidArgumentException('Data array cannot be empty');
        }

        try {
            $documentPath = "{$collection}/{$documentId}";
            $fieldPaths = $this->getFieldPaths($data);
            $queryParams = [];
            foreach ($fieldPaths as $fieldPath) {
                $queryParams[] = 'updateMask.fieldPaths='.urlencode($fieldPath);
            }
            $queryString = implode('&', $queryParams);

            \Log::debug('Firestore updateDocument request', [
                'documentPath' => $documentPath,
                'fieldPaths' => $fieldPaths,
                'data' => $data,
                'queryString' => $queryString,
            ]);

            $response = $this->httpClient->patch("{$documentPath}?{$queryString}", [
                'json' => ['fields' => $this->transformData($data)],
            ]);

            $body = json_decode($response->getBody(), true);
            \Log::info('Firestore updateDocument successful', [
                'documentPath' => $documentPath,
                'response' => $body,
            ]);

            return $body ?? null;
        } catch (RequestException $e) {
            \Log::error('Firestore updateDocument failed: '.$e->getMessage(), [
                'collection' => $collection,
                'documentId' => $documentId,
                'data' => $data,
                'queryString' => $queryString,
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ]);

            return null;
        }
    }

    public function deleteDocument(string $collection, string $documentId): bool
    {
        try {
            $path = "{$collection}/{$documentId}";
            Log::debug('Attempting to delete Firestore document', [
                'url' => $this->httpClient->getConfig('base_uri').$path,
            ]);

            $response = $this->httpClient->delete($path);

            Log::info('Document deleted successfully', ['status' => $response->getStatusCode()]);

            return true;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $body = $e->hasResponse() ? (string) $e->getResponse()->getBody() : null;
            Log::error('Error deleting document', ['error' => $e->getMessage(), 'body' => $body]);

            return false;
        }
    }

    public function getDocumentGrpc(string $documentPath): ?array
    {
        try {
            $response = $this->httpClient->get($documentPath);

            return json_decode($response->getBody(), true) ?? null;
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                return null;
            }
            \Log::error('Firestore getDocument failed: '.$e->getMessage(), [
                'documentPath' => $documentPath,
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ]);

            return null;
        }
    }

    public function getDocument(string $documentPath): ?array
    {
        try {
            $response = $this->httpClient->get($documentPath);
            $docData = json_decode($response->getBody(), true);

            return ! empty($docData) ? $docData : null;
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                return null;
            }
            \Log::error('Firestore getDocument failed: '.$e->getMessage(), [
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ]);

            return null;
        }
    }

    protected function getFieldPaths(array $data): array
    {
        $fieldPaths = [];

        foreach ($data as $key => $value) {
            if (is_array($value) && $this->isAssoc($value)) {
                foreach ($this->getFieldPaths($value) as $subPath) {
                    $fieldPaths[] = "{$key}.{$subPath}";
                }
            } else {
                $fieldPaths[] = $key;
            }
        }

        return $fieldPaths;
    }

    protected function transformData(array $data)
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[$key] = $this->transformValue($value);
        }

        return $fields;
    }

    protected function transformValue($value)
    {
        if (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_int($value)) {
            return ['integerValue' => (string) $value];
        } elseif (is_float($value)) {
            return ['doubleValue' => $value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif ($value instanceof \DateTime) {
            return ['timestampValue' => $value->format(\DateTime::RFC3339)];
        } elseif (is_array($value)) {
            if ($this->isAssoc($value)) {
                return ['mapValue' => ['fields' => $this->transformData($value)]];
            } else {
                return [
                    'arrayValue' => [
                        'values' => array_map(function ($item) {
                            return $this->transformValue($item);
                        }, $value),
                    ],
                ];
            }
        } elseif ($value instanceof \stdClass) {
            return ['mapValue' => ['fields' => $this->transformData((array) $value)]];
        } else {
            return ['stringValue' => (string) $value];
        }
    }

    protected function isAssoc(array $arr)
    {
        if ($arr === []) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
