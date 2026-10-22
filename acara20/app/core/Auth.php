<?php
class Auth
{
    // Ganti dengan string rahasia yang panjang dan acak untuk project sendiri.
    private const SECRET_KEY = 'GANTI_DENGAN_SECRET_RANDOM_MINIMAL_32_KARAKTER';
    private const TOKEN_TTL = 3600; // 1 jam

    public static function generateToken(array $payload): string
    {
        $now = time();
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['iat'] = $now;
        $payload['exp'] = $now + self::TOKEN_TTL;

        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            self::SECRET_KEY,
            true
        );

        return $headerEncoded . '.' . $payloadEncoded . '.' . self::base64UrlEncode($signature);
    }

    public static function requireAuth(): array
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if ($authHeader === '' && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if ($authHeader === '' && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (!preg_match('/^Bearer\s+(\S+)$/i', trim($authHeader), $matches)) {
            Response::json(401, [
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ]);
        }

        $payload = self::validateToken($matches[1]);
        if ($payload === false) {
            Response::json(401, [
                'success' => false,
                'message' => 'Token tidak valid atau kedaluwarsa'
            ]);
        }

        return $payload;
    }

    private static function validateToken(string $token): array|false
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        $expectedSignature = self::base64UrlEncode(hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            self::SECRET_KEY,
            true
        ));

        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            return false;
        }

        $payloadJson = self::base64UrlDecode($payloadEncoded);
        if ($payloadJson === false) {
            return false;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload) || !isset($payload['exp']) || time() >= (int) $payload['exp']) {
            return false;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string|false
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        return $decoded === false ? false : $decoded;
    }
}
