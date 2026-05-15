<?php

use Google\Auth\Credentials\ServiceAccountCredentials;

function send_fcm_notification_v1(array $tokens = [], string $title = '', string $body = '', string $type = 'general', string $status = 'SUCCESS')
{

    try {

        if (empty($tokens)) {
            return false;
        }

        // =====================================
        // FIREBASE CONFIG
        // =====================================
        $serviceAccountPath = APPPATH . 'config/firebase/service-account.json';
        $projectId = 'codrin-terra-erp';

        // =====================================
        // SCOPES
        // =====================================
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        // =====================================
        // CREDENTIALS
        // =====================================
        $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);

        // =====================================
        // ACCESS TOKEN
        // =====================================
        $token = $credentials->fetchAuthToken();
        $accessToken = $token['access_token'];

        // =====================================
        // RESULTS
        // =====================================
        $results = [];

        // =====================================
        // LOOP TOKENS
        // =====================================
        foreach ($tokens as $fcmToken) {

            $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

            $payload = [
                'message' => [
                    'token' => $fcmToken,

                    'android' => [
                        'priority' => 'high'
                    ],

                    'data' => [
                        'title' => $title,
                        'body' => $body,
                        'type' => $type,
                        'status' => $status
                    ]
                ]
            ];

            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ];

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($payload)
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $results[] = [
                'token' => $fcmToken,
                'statusCode' => $statusCode,
                'response' => $response,
                'error' => $error
            ];
        }
        return $results;
    } catch (Throwable $e) {
        log_message('error', $e->getMessage());
        return false;
    }
}
