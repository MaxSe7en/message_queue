<?php
namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerService
{
    private static ?Logger $logger = null;

    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger('app');
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
        }

        return self::$logger;
    }

    public static function logInfo(string $message, array $context = []): void
    {
        self::getLogger()->info($message, $context);
    }

    public static function logError(string $message, array $context = []): void
    {
        self::getLogger()->error($message, $context);
    }
}
