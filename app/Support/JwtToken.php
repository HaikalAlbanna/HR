<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class JwtToken
{
    public static function make(array $payload, int $ttlSeconds = 60): string
    {
        $now = time();
        $payload = array_merge($payload, [
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
        ]);

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $unsigned = self::base64UrlEncode(json_encode($header)) . '.' . self::base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $unsigned, self::secret(), true);

        return $unsigned . '.' . self::base64UrlEncode($signature);
    }

    public static function parse(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Format token tidak valid.');
        }

        [$header, $payload, $signature] = $parts;
        $unsigned = $header . '.' . $payload;
        $expected = self::base64UrlEncode(hash_hmac('sha256', $unsigned, self::secret(), true));

        if (!hash_equals($expected, $signature)) {
            throw new InvalidArgumentException('Signature token tidak valid.');
        }

        $data = json_decode(self::base64UrlDecode($payload), true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Payload token tidak valid.');
        }

        if (($data['exp'] ?? 0) < time()) {
            throw new InvalidArgumentException('Token sudah kedaluwarsa.');
        }

        return $data;
    }

    private static function secret(): string
    {
        $key = Config::get('app.key', '');

        if (str_starts_with($key, 'base64:')) {
            return base64_decode(substr($key, 7)) ?: $key;
        }

        return $key;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string
    {
        return base64_decode(strtr($value, '-_', '+/')) ?: '';
    }
}
