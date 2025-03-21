<?php

namespace Pandao\Core\Services;

class Csrf
{
    /**
     * Generate and retrieve a CSRF token.
     * If the token does not exist in the session, a new one is generated.
     *
     * @return string The generated or existing CSRF token.
     */
    public static function generateToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify the CSRF token sent via a form.
     *
     * @param string $type The type of request ('post' or 'get') to check.
     * @return bool Returns true if the token is valid, false otherwise.
     */
    public static function verifyToken($type)
    {
        $token = ($type == 'post') ? $_POST['csrf_token'] ?? false : ($type == 'get' ? $_GET['csrf_token'] ?? false : false);

        if ($token && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        return false;
    }

    /**
     * Invalidate the CSRF token, typically after successful validation or session expiration.
     */
    public static function invalidateToken()
    {
        unset($_SESSION['csrf_token']);
    }
}
