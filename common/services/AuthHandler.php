<?php

namespace Pandao\Common\Services;

class AuthHandler
{
    /**
     * Log in the user by storing user information in session.
     *
     * @param array $user Associative array containing user data.
     */
    public static function login($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user']['id'] = $user['id'];
        $_SESSION['user']['login'] = $user['login'];
        $_SESSION['user']['type'] = $user['type'];
        $_SESSION['user']['email'] = $user['email'];

        $_SESSION['user']['login_time'] = time();
    }

    /**
     * Check if the user is authenticated.
     *
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']['id']);
    }

    /**
     * Log out the user by destroying the session.
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }
}
