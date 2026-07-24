<?php

namespace App\Http\Controllers\Traits;

use App\Models\GeneralSetting;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait PushNotificationTrait
{
    /**
     * Send a push notification to a single device via FCM.
     *
     * @param  string  $deviceToken
     * @param  string  $title
     * @param  string  $body
     * @param  array  $data  Optional data payload
     * @return void
     */
    public function sendFcmMessage($deviceToken, $subject, $message, $data = [], $vendorNotification = 0, $userType = null)
    {
        $traceId = (string) Str::uuid();
        if (empty($deviceToken)) {
            Log::warning('FCM send skipped: empty device token', ['trace_id' => $traceId]);
            return false;
        }

        // Keep caller-provided payload (e.g. food_order route/id) and only
        // backfill booking route/status when those keys are absent.
        $payloadData = is_array($data) ? $data : [];
        $parsedBookingData = $this->parseBookingData($data);
        foreach ($parsedBookingData as $key => $value) {
            if (! array_key_exists($key, $payloadData) || $payloadData[$key] === null || $payloadData[$key] === '') {
                $payloadData[$key] = $value;
            }
        }
        $payloadData['vendorNotification'] = (string) $vendorNotification;
        $payloadData = $this->normalizeFcmDataPayload($payloadData);
        Log::info('FCM send start', [
            'trace_id' => $traceId,
            'device_token_preview' => substr($deviceToken, 0, 20).'...',
            'subject' => (string) $subject,
            'user_type' => $userType,
            'route' => $payloadData['route'] ?? null,
        ]);

        $firebaseConfig = $this->getFirebaseNotificationConfig();
        if (! $firebaseConfig) {
            Log::error('FCM send failed: firebase config missing', ['trace_id' => $traceId]);
            return false;
        }

        $accessToken = $this->fetchFirebaseAccessToken($firebaseConfig['firebase_credentials_json']);
        if (! $accessToken) {
            Log::error('FCM send failed: cannot generate firebase access token', ['trace_id' => $traceId]);
            return false;
        }

        $endpoint = 'https://fcm.googleapis.com/v1/projects/'.$firebaseConfig['project_id'].'/messages:send';

        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => (string) $subject,
                    'body' => (string) $message,
                ],
                'data' => $payloadData,
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'high_importance_channel',
                        'sound' => 'default',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($endpoint, $payload);

        if ($response->failed()) {
            Log::error('FCM v1 send failed', [
                'trace_id' => $traceId,
                'status' => $response->status(),
                'body' => $response->body(),
                'project_id' => $firebaseConfig['project_id'],
                'device_token_preview' => substr($deviceToken, 0, 20).'...',
            ]);

            return false;
        }

        $responseJson = $response->json();
        Log::info('FCM v1 send success', [
            'trace_id' => $traceId,
            'project_id' => $firebaseConfig['project_id'],
            'deviceTokenPreview' => substr($deviceToken, 0, 20).'...',
            'fcm_message_name' => $responseJson['name'] ?? null,
            'response_body' => $response->body(),
        ]);

        return true;
    }

    /**
     * Send a push notification to multiple devices via FCM.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  array  $data  Optional data payload
     * @return void
     */
    public function sendPushNotificationsToDevices(array $deviceTokens, $title, $body, $data = [])
    {
        foreach ($deviceTokens as $token) {
            $this->sendFcmMessage($token, $title, $body, $data);
        }
    }

    /**
     * Send a push notification to all devices subscribed to a particular topic via FCM.
     *
     * @param  string  $topic
     * @param  string  $title
     * @param  string  $body
     * @param  array  $data  Optional data payload
     * @return void
     */
    public function sendPushNotificationToTopic($topic, $title, $body, $data = [])
    {
        $firebaseConfig = $this->getFirebaseNotificationConfig();
        if (! $firebaseConfig) {
            Log::error('FCM topic send failed: firebase config missing');
            return false;
        }
        $accessToken = $this->fetchFirebaseAccessToken($firebaseConfig['firebase_credentials_json']);
        if (! $accessToken) {
            Log::error('FCM topic send failed: cannot generate firebase access token');
            return false;
        }

        $endpoint = 'https://fcm.googleapis.com/v1/projects/'.$firebaseConfig['project_id'].'/messages:send';
        $payload = [
            'message' => [
                'topic' => (string) $topic,
                'notification' => [
                    'title' => (string) $title,
                    'body' => (string) $body,
                ],
                'data' => $this->normalizeFcmDataPayload($data),
            ],
        ];

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($endpoint, $payload);

        if ($response->failed()) {
            Log::error('FCM v1 topic send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'topic' => $topic,
            ]);

            return false;
        }

        return true;
    }

    private function getFirebaseNotificationConfig()
    {
        $settings = GeneralSetting::whereIn('meta_key', [
            'firebase_project_id',
            'firebase_firebase_credentials_json_path',
        ])->pluck('meta_value', 'meta_key')->toArray();

        $serviceAccountPath = $settings['firebase_firebase_credentials_json_path'] ?? env('FIREBASE_firebase_credentials_JSON_PATH') ?? 'firebase/firebase_credentials.json';
        if (empty($serviceAccountPath)) {
            return null;
        }

        $resolvedPath = $this->resolveFirebaseServiceAccountPath($serviceAccountPath);
        if (! $resolvedPath || ! file_exists($resolvedPath)) {
            Log::error('FCM service account file not found', [
                'path' => $serviceAccountPath,
                'storage_candidate' => storage_path('app/'.$serviceAccountPath),
                'storage_root_candidate' => storage_path($serviceAccountPath),
                'base_storage_candidate' => base_path('storage/'.$serviceAccountPath),
                'public_storage_candidate' => public_path('storage/'.$serviceAccountPath),
                'public_html_candidate' => base_path('public_html/storage/'.$serviceAccountPath),
                'public_html_parent_candidate' => base_path('../public_html/storage/'.$serviceAccountPath),
            ]);
            return null;
        }

        $json = json_decode(file_get_contents($resolvedPath), true);
        if (! is_array($json)) {
            Log::error('FCM service account json invalid', ['path' => $resolvedPath]);
            return null;
        }

        $projectId = $settings['firebase_project_id'] ?? ($json['project_id'] ?? env('FIREBASE_PROJECT_ID'));
        if (empty($projectId)) {
            Log::error('FCM project id missing');
            return null;
        }

        Log::info('FCM firebase config resolved', [
            'project_id' => $projectId,
            'firebase_credentials_path' => $resolvedPath,
        ]);

        return [
            'project_id' => $projectId,
            'firebase_credentials_json' => $json,
        ];
    }

    private function resolveFirebaseServiceAccountPath($path)
    {
        if (empty($path)) {
            return null;
        }

        if (file_exists($path)) {
            return $path;
        }

        $storageCandidate = storage_path('app/'.$path);
        if (file_exists($storageCandidate)) {
            return $storageCandidate;
        }

        $storageRootCandidate = storage_path($path);
        if (file_exists($storageRootCandidate)) {
            return $storageRootCandidate;
        }

        $baseStorageCandidate = base_path('storage/'.$path);
        if (file_exists($baseStorageCandidate)) {
            return $baseStorageCandidate;
        }

        $publicStorageCandidate = public_path('storage/'.$path);
        if (file_exists($publicStorageCandidate)) {
            return $publicStorageCandidate;
        }

        $publicPathCandidate = public_path($path);
        if (file_exists($publicPathCandidate)) {
            return $publicPathCandidate;
        }

        $publicHtmlStorageCandidate = base_path('public_html/storage/'.$path);
        if (file_exists($publicHtmlStorageCandidate)) {
            return $publicHtmlStorageCandidate;
        }

        $publicHtmlStorageParentCandidate = base_path('../public_html/storage/'.$path);
        if (file_exists($publicHtmlStorageParentCandidate)) {
            return $publicHtmlStorageParentCandidate;
        }

        return null;
    }

    private function fetchFirebaseAccessToken(array $serviceAccountJson)
    {
        try {
            $client = new GoogleClient;
            $client->setAuthConfig($serviceAccountJson);
            $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);
            $token = $client->fetchAccessTokenWithAssertion();

            return $token['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::error('FCM access token generation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function normalizeFcmDataPayload(array $data)
    {
        $normalized = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $normalized[(string) $key] = json_encode($value);
            } elseif (is_bool($value)) {
                $normalized[(string) $key] = $value ? '1' : '0';
            } elseif ($value === null) {
                $normalized[(string) $key] = '';
            } else {
                $normalized[(string) $key] = (string) $value;
            }
        }

        return $normalized;
    }

    private function parseBookingData($data)
    {

        $checkIn = $this->extractValue($data, 'check_in');

        if ($checkIn !== null) {
            $bookingStatus = $this->extractValue($data, 'status') ?? 'Unknown';

            return [
                'status' => $bookingStatus,
                'route' => 'booking',
            ];
        }
        $guestRating = $this->extractValue($data, 'guest_rating');
        $hostRating = $this->extractValue($data, 'host_rating');

        // If either guest_rating or host_rating exists, set route to 'review'
        if ($guestRating !== null || $hostRating !== null) {
            return [
                'route' => 'review',
            ];
        }

        return [

            'route' => 'none',
        ];
    }

    private function extractValue($data, $key)
    {

        if (is_array($data) || is_object($data)) {

            if (isset($data[$key])) {
                return $data[$key];
            }

            foreach ($data as $item) {
                $result = $this->extractValue($item, $key);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }
}
