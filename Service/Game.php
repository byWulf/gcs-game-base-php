<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service;

use ByWulf\GameCentralStation\Event\Backend as BackendEvent;
use ByWulf\GameCentralStation\Event\Game as GameEvent;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\Helper;
use ByWulf\GameCentralStation\Internal;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Game
 * @package Bywulf\GameCentralStation\Service
 */
class Game
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @throws BackendCommunicatorException
     */
    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();

        Internal::getBackendCommunicator()->on(BackendEvent\Match\MatchRequestingAuthEvent::class, function() {
            global $argv;
            Internal::getBackendCommunicator()->sendCommand(BackendCommunicator::COMMAND_MATCH_AUTH, $argv[1]);
        });

        Internal::getBackendCommunicator()->on(BackendEvent\Match\MatchAuthedEvent::class, function (BackendEvent\Match\MatchAuthedEvent $event) {
            $this->settings = $event->getSettings();
        });

        Internal::getBackendCommunicator()->on(BackendEvent\Match\MatchStartedEvent::class, function () {
            $this->eventDispatcher->dispatch(new GameEvent\MatchStartedEvent());
        });

        Internal::getBackendCommunicator()->on(BackendEvent\Match\MatchErrorEvent::class, function (BackendEvent\Match\MatchErrorEvent $event) {
            Helper::getLogger()->error('Error from Backend: ' . $event->getKey());
            exit(1);
        });

        Internal::getBackendCommunicator()->on(BackendEvent\Match\MatchTerminateEvent::class, function () {
            Helper::getLogger()->info('Game terminated by backend. Bye bye!');
            exit(0);
        });
    }

    /**
     * @param callable $callback
     */
    public function onStart(callable $callback): void
    {
        $this->eventDispatcher->addListener(GameEvent\MatchStartedEvent::class, $callback);
    }
}