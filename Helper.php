<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class Helper
 * @package ByWulf\GameCentralStation
 */
class Helper
{
    /**
     * @var LoggerInterface
     */
    private static $logger;

    /**
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public static function getLogger(): LoggerInterface
    {
        if (self::$logger === null) {
            self::$logger = new Logger('game');
            try {
                $handler = new StreamHandler('php://stdout', Logger::DEBUG);
                $handler->setFormatter(new LineFormatter());
                self::$logger->pushHandler($handler);
            } catch (Exception $e) {
            }
        }

        return self::$logger;
    }
}