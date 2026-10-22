<?php
class Response
{
    /**
     * Mengirim respons JSON dengan format yang konsisten.
     * $payload berisi success, message, dan data.
     * $errors berisi rincian kesalahan validasi per field.
     */
    public static function json(int $status, array $payload = [], array $errors = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        // Mendukung errors yang dikirim lewat parameter ketiga atau payload.
        if ($errors === [] && isset($payload['errors']) && is_array($payload['errors'])) {
            $errors = $payload['errors'];
        }

        $response = [
            'success' => (bool) ($payload['success'] ?? ($status >= 200 && $status < 300)),
            'message' => (string) ($payload['message'] ?? ''),
            'data' => $payload['data'] ?? null,
            'errors' => $errors,
            'timestamp' => date('c'),
        ];

        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $flags |= JSON_PRETTY_PRINT;
        }

        echo json_encode($response, $flags);
        exit;
    }
}
