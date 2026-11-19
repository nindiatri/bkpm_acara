<?php
class Response
{
    public static function json(int $status, array $payload): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        // Semua response memiliki field errors dan timestamp agar formatnya konsisten.
        if (!array_key_exists('errors', $payload)) {
            $payload['errors'] = [];
        }
        $payload['timestamp'] = date('c');

        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        // JSON_PRETTY_PRINT hanya aktif pada mode development.
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $options |= JSON_PRETTY_PRINT;
        }

        echo json_encode($payload, $options);
        exit;
    }
}
