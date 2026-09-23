<?php

function env($key, $default = null)
{
    return Env::get($key, $default);
}

function url($path = '')
{
    $baseUrl = Env::get('APP_URL', BASE_URL);
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function old($key, $default = '')
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['old'][$key] ?? $default;
}

function saveOldInput()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['old'] = $_POST;
}

function clearOldInput()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['old']);
}

function dd(...$vars)
{
    echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 15px; border-radius: 5px; overflow: auto;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

function formatPrice($amount, $currency = 'FCFA')
{
    return number_format((float)$amount, 0, ',', ' ') . ' ' . $currency;
}

function formatDate($date, $format = 'd/m/Y')
{
    if (empty($date)) {
        return '';
    }
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

function formatDateTime($datetime, $format = 'd/m/Y H:i')
{
    if (empty($datetime)) {
        return '';
    }
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    return date($format, $timestamp);
}

function sanitizeFilename($filename)
{
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    $filename = preg_replace('/_+/', '_', $filename);
    return trim($filename, '_');
}

function uploadFile($file, $directory = 'uploads', $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
{
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new Exception('Paramètres invalides');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('Aucun fichier envoyé');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('Fichier trop volumineux');
        default:
            throw new Exception('Erreur inconnue lors de l\'upload');
    }

    $maxSize = Env::get('UPLOAD_MAX_SIZE', 5242880);
    if ($file['size'] > $maxSize) {
        throw new Exception('Le fichier dépasse la taille maximale autorisée (' . ($maxSize / 1024 / 1024) . ' MB)');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Type de fichier non autorisé');
    }

    $extension = match ($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        default => pathinfo($file['name'], PATHINFO_EXTENSION)
    };

    $uploadDir = dirname(__DIR__) . '/' . trim($directory, '/');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = sprintf(
        '%s_%s.%s',
        date('YmdHis'),
        bin2hex(random_bytes(8)),
        $extension
    );

    $destination = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Échec du déplacement du fichier uploadé');
    }

    return $directory . '/' . $filename;
}

function deleteFile($path)
{
    $fullPath = dirname(__DIR__) . '/' . ltrim($path, '/');
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

function isProduction()
{
    return Env::get('APP_ENV') === 'production';
}

function isDebug()
{
    return Env::get('APP_DEBUG', false) === 'true' || Env::get('APP_DEBUG', false) === true;
}
