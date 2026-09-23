<?php

class CSRF
{
    private static $tokenName = '_csrf_token';
    private static $tokenTime = '_csrf_token_time';
    private static $tokenExpiry = 3600;

    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::$tokenName] = $token;
        $_SESSION[self::$tokenTime] = time();

        return $token;
    }

    public static function getToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::$tokenName]) || !isset($_SESSION[self::$tokenTime])) {
            return self::generateToken();
        }

        if (time() - $_SESSION[self::$tokenTime] > self::$tokenExpiry) {
            return self::generateToken();
        }

        return $_SESSION[self::$tokenName];
    }

    public static function validateToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::$tokenName]) || !isset($_SESSION[self::$tokenTime])) {
            return false;
        }

        if (time() - $_SESSION[self::$tokenTime] > self::$tokenExpiry) {
            return false;
        }

        return hash_equals($_SESSION[self::$tokenName], $token);
    }

    public static function field()
    {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verifyRequest()
    {
        $token = $_POST[self::$tokenName] ?? $_GET[self::$tokenName] ?? null;

        if (!$token || !self::validateToken($token)) {
            Logger::warning('Tentative de requête avec token CSRF invalide', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ]);

            http_response_code(403);
            die('Erreur de sécurité : Token CSRF invalide. Veuillez réessayer.');
        }

        return true;
    }
}
