<?php

class Logger
{
    private static $logPath;
    private static $logLevel;
    private static $levels = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
        'critical' => 4
    ];

    private static function init()
    {
        if (self::$logPath === null) {
            self::$logPath = dirname(__DIR__) . '/' . Env::get('LOG_PATH', 'logs/app.log');
            self::$logLevel = Env::get('LOG_LEVEL', 'debug');

            $logDir = dirname(self::$logPath);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
        }
    }

    private static function shouldLog($level)
    {
        self::init();
        $currentLevel = self::$levels[self::$logLevel] ?? 0;
        $messageLevel = self::$levels[$level] ?? 0;
        return $messageLevel >= $currentLevel;
    }

    private static function write($level, $message, $context = [])
    {
        if (!self::shouldLog($level)) {
            return;
        }

        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        $isProduction = Env::get('APP_ENV') === 'production';
        if ($isProduction && in_array($level, ['error', 'critical'])) {
            error_log($logMessage);
        }

        file_put_contents(self::$logPath, $logMessage, FILE_APPEND | LOCK_EX);
    }

    public static function debug($message, $context = [])
    {
        self::write('debug', $message, $context);
    }

    public static function info($message, $context = [])
    {
        self::write('info', $message, $context);
    }

    public static function warning($message, $context = [])
    {
        self::write('warning', $message, $context);
    }

    public static function error($message, $context = [])
    {
        self::write('error', $message, $context);
    }

    public static function critical($message, $context = [])
    {
        self::write('critical', $message, $context);
    }

    public static function logException(Throwable $e, $context = [])
    {
        $message = sprintf(
            '%s: %s in %s:%d',
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        $context['trace'] = $e->getTraceAsString();
        self::error($message, $context);
    }
}
