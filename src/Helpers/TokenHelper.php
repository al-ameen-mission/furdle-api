<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Token Helper for generating and validating tokens.
 */
class TokenHelper
{
    /**
     * Generate access and refresh tokens.
     *
     * @return array
     */
    public static function generate(): array
    {
        $accessToken = bin2hex(random_bytes(32));
        $refreshToken = bin2hex(random_bytes(32));

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
        // Dummy validation: check if token is at least 10 characters
        return strlen($token) >= 10;
    }

    /**
     * Decode a token to get user data.
     *
     * @param string $token
     * @return array|null
     */
    public static function decode(string $token): ?array
    {
        if (!self::validate($token)) {
            return null;
        }

        // Dummy decode: return user data
        return [
            'id' => '123',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ];
    }
}