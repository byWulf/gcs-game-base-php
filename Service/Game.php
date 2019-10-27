<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service;

use Bywulf\GameCentralStation\Model\Slot;
use ByWulf\GameCentralStation\Event\Backend as BackendEvent;
use ByWulf\GameCentralStation\Event\Game as GameEvent;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\GameBase;
use ByWulf\GameCentralStation\Helper;
use ByWulf\GameCentralStation\Internal;
use InvalidArgumentException;
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
     * @var GameBase
     */
    private $gameBase;

    /**
     * @param GameBase $gameBase
     * @throws BackendCommunicatorException
     */
    public function __construct(GameBase $gameBase)
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->gameBase = $gameBase;

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

    /**
     * @throws BackendCommunicatorException
     */
    public function finish(): void
    {
        $slots = $this->gameBase->getPlayers()->getSlots();

        /** @var int $maxPoints */
        $maxPoints = array_reduce($slots, function(?int $maxPoints, Slot $slot) {
            if ($maxPoints === null) {
                return $slot->getPoints();
            }
            return max($maxPoints, $slot->getPoints());
        });

        /** @var Slot[] $winners */
        $winners = array_filter($slots, function(Slot $slot) use ($maxPoints) {
            return $slot->getPoints() === $maxPoints;
        });

        $winnerString = '';
        foreach (array_values($winners) as $i => $winner) {
            if ($i > 0 && $i === count($winners) - 1) {
                $winnerString .= ' und ';
            } elseif ($i > 0) {
                $winnerString .= ', ';
            }

            $winnerString .= '%' . $winner->getIndex();
        }

        if (count($winners) > 0) {
            $winnerString .= ' haben die Partie gewonnen. Herzlichen Glückwunsch!';
        } else {
            $winnerString .= ' hat die Partie gewonnen. Herzlichen Glückwunsch!';
        }

        $this->addGlobalNotification($winnerString);
        Internal::getBackendCommunicator()->sendCommand('finish');
    }

    /**
     * @param float $progress
     * @throws BackendCommunicatorException
     */
    public function setProgress(float $progress): void
    {
        if ($progress < 0 || $progress > 1) {
            throw new InvalidArgumentException('Progress must be between 0 and 1.');
        }

        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'progress.changed',
            'progress' => $progress
        ]);
    }

    /**
     * @param string $message
     * @throws BackendCommunicatorException
     */
    public function setStatusMessage(string $message): void
    {
        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'statusMessage.changed',
            'text' => $message
        ]);
    }

    /**
     * @param string $message
     * @throws BackendCommunicatorException
     */
    public function addGlobalNotification(string $message): void
    {
        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'notification.added',
            'text' => $message,
            'targetPlayers' => null
        ]);
    }
}