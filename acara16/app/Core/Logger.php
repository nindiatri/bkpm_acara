<?php

class Logger
{
    public static function error(Throwable $e): void
    {
        $logDirectory = __DIR__ . '/../../storage/logs';

        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        $logFile = $logDirectory . '/app.log';

        $message = sprintf(
            "[%s] %s in %s:%d%s",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            PHP_EOL
        );

        error_log($message, 3, $logFile);
    }
}
