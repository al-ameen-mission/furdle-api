<?php

declare(strict_types=1);

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Token Helper for generating and validating JWT tokens.
 */
class TokenHelper
{
    private const SECRET_KEY = 'your-secret-key-here-change-in-production';
    private const ALGORITHM = 'HS256';

    /**
     * Generate access and refresh tokens.
     *
     * @param array $payload
     * @return array
     */
    public static function generate(array $payload): array
    {
        $tokenId = rand(1111111111, 9999999999);

        $accessPayload = [
            'iss' => 'your-app',
            'aud' => 'your-app-users',
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
            'jti' => $tokenId,
            'type' => 'access',
            'user' => $payload
        ];

        $refreshPayload = [
            'iss' => 'your-app',
            'aud' => 'your-app-users',
            'iat' => time(),
            'exp' => time() + 604800, // 1 week
            'jti' => $tokenId,
            'type' => 'refresh',
            'access_token_id' => $tokenId
        ];

        $accessToken = JWT::encode($accessPayload, self::SECRET_KEY, self::ALGORITHM);
        $refreshToken = JWT::encode($refreshPayload, self::SECRET_KEY, self::ALGORITHM);

        return [
            'access' => $accessToken,
            'refresh' => $refreshToken
        ];
    }

    /**
     * Validate a token.
     *
     * @param string $token
     * @return bool
     */
    public static function validate(string $token): bool
    {
        try {
            JWT::decode($token, new Key(self::SECRET_KEY, self::ALGORITHM));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Decode a token to get user data.
     *
     * @param string $token
     * @return array|null
     */
    public static function decode(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::SECRET_KEY, self::ALGORITHM));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
