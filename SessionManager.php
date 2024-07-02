<?php

class SessionManager {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::startSession();
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        self::startSession();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function remove($key) {
        self::startSession();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroySession() {
        if (session_status() != PHP_SESSION_NONE) {
            session_destroy();
        }
    }
}

?>
