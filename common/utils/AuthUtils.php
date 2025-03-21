<?php

namespace Pandao\Common\Utils;

/**
 * Class AuthUtils
 * - getToken
 * - checkToken
 * - genPass
 */

class AuthUtils
{
    /**
     * Generates a unique token for a given name.
     *
     * @param string $name The name to associate with the token.
     * @return string The generated token.
     */
    public static function getToken($name)
    {
        $token = uniqid(rand(), true);
        $_SESSION[$name . '_token'] = $token;
        $_SESSION[$name . '_token_time'] = time();
        return $token;
    }

    /**
     * Validates a CSRF token for a given request type.
     *
     * @param string $referer The referer URL to validate against.
     * @param string $name The name associated with the token.
     * @param string $type The request type ('post' or 'get').
     * @return bool True if the token is valid, otherwise false.
     */
    public static function checkToken($referer, $name, $type)
    {
        if (isset($_SESSION[$name . '_token']) && isset($_SESSION[$name . '_token_time'])
            && (($type == 'post' && isset($_POST['csrf_token']) && $_SESSION[$name . '_token'] == $_POST['csrf_token'])
            XOR ($type == 'get' && isset($_GET['csrf_token']) && $_SESSION[$name . '_token'] == $_GET['csrf_token']))
            && ($_SESSION[$name . '_token_time'] >= (time() - 1800))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generates a random password.
     *
     * @param int $len The length of the generated password (default is 8).
     * @return string The generated password.
     */
    public static function genPass($len = 8)
    {
        if ($len < 6) $len = 6;

        $lowercase = 'abcdefghijkmnpqrstuvwxyz';
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $digits = '123456789';
        $specials = '!%$_&*+=@/"?#';

        $pass = [];

        $pass[] = $lowercase[random_int(0, strlen($lowercase) - 1)];
        $pass[] = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $pass[] = $digits[random_int(0, strlen($digits) - 1)];
        $pass[] = $specials[random_int(0, strlen($specials) - 1)];

        $all_chars = $lowercase . $uppercase . $digits . str_replace($specials[random_int(0, strlen($specials) - 1)], '', $specials);

        while (count($pass) < $len) {
            $next_char = $all_chars[random_int(0, strlen($all_chars) - 1)];
            if (end($pass) !== $next_char) {
                $pass[] = $next_char;
            }
        }
        shuffle($pass);

        return implode('', $pass);
    }

    /**
     * Checks if a session has been started.
     *
     * @return bool True if a session is active, otherwise false.
     */
    public static function isSessionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        }
        return false;
    }
}
