<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation;

use Bywulf\GameCentralStation\Service\BackendCommunicator;

/**
 * Class Internal
 * @package ByWulf\GameCentralStation
 *
 * @internal
 */
class Internal
{
    /**
     * @var BackendCommunicator
     */
    private static $backendCommunicator;

    /**
     * Used for testing purposes.
     *
     * @param BackendCommunicator $backendCommunicator
     */
    public static function setBackendCommunicator(BackendCommunicator $backendCommunicator): void
    {
        self::$backendCommunicator = $backendCommunicator;
    }

    /**
     * @return BackendCommunicator
     */
    public static function getBackendCommunicator(): BackendCommunicator
    {
        if (self::$backendCommunicator === null) {
            self::$backendCommunicator = new BackendCommunicator();
        }

        return self::$backendCommunicator;
    }
}