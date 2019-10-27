<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service;

use Bywulf\GameCentralStation\Model\Slot;
use ByWulf\GameCentralStation\Event\Backend as BackendEvent;
use ByWulf\GameCentralStation\Event\Players as PlayersEvent;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\GameBase;
use ByWulf\GameCentralStation\Internal;
use ByWulf\GameCentralStation\Service\Slot\SlotManipulator;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Players
 * @package Bywulf\GameCentralStation\Service
 */
class Players
{
    /**
     * @var Slot[]
     */
    private $slots;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var SlotManipulator
     */
    private $slotManipulator;
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
        $this->slotManipulator = new SlotManipulator();
        $this->gameBase = $gameBase;

        Internal::getBackendCommunicator()->on(BackendEvent\Match\MatchAuthedEvent::class, function (BackendEvent\Match\MatchAuthedEvent $event) {
            $this->slots = $event->getSlots();
        });

        Internal::getBackendCommunicator()->on(BackendEvent\User\UserJoinedEvent::class, function(BackendEvent\User\UserJoinedEvent $event) {
            $this->slotManipulator->setUser($this->getSlot($event->getSlotIndex()), $event->getUser());
            
            $this->eventDispatcher->dispatch(new PlayersEvent\UserJoinedEvent($event->getSlotIndex(), $event->getUser()));
        });

        Internal::getBackendCommunicator()->on(BackendEvent\User\UserSwitchedEvent::class, function(BackendEvent\User\UserSwitchedEvent $event) {
            $this->slotManipulator->setUser($this->getSlot($event->getOldSlotIndex()), null);
            $this->slotManipulator->setUser($this->getSlot($event->getNewSlotIndex()), $event->getUser());

            $this->eventDispatcher->dispatch(new PlayersEvent\UserSwitchedEvent($event->getOldSlotIndex(), $event->getNewSlotIndex(), $event->getUser()));
        });

        Internal::getBackendCommunicator()->on(BackendEvent\User\UserLeftEvent::class, function(BackendEvent\User\UserLeftEvent $event) {
            $this->slotManipulator->setUser($this->getSlot($event->getSlotIndex()), $event->getUser());

            $this->eventDispatcher->dispatch(new PlayersEvent\UserLeftEvent($event->getSlotIndex(), $event->getUser()));
        });
    }

    /**
     * @param callable $callback
     */
    public function onJoin(callable $callback): void
    {
        $this->eventDispatcher->addListener(PlayersEvent\UserJoinedEvent::class, $callback);
    }

    /**
     * @param callable $callback
     */
    public function onSwitch(callable $callback): void
    {
        $this->eventDispatcher->addListener(PlayersEvent\UserSwitchedEvent::class, $callback);
    }

    /**
     * @param callable $callback
     */
    public function onLeave(callable $callback): void
    {
        $this->eventDispatcher->addListener(PlayersEvent\UserLeftEvent::class, $callback);
    }

    /**
     * @return Slot[]
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * @param int $index
     * @return Slot
     */
    public function getSlot(int $index): Slot
    {
        return $this->slots[$index];
    }

    /**
     * @return Slot[]
     */
    public function getFilledSlots(): array
    {
        return array_filter($this->slots, function(Slot $slot) {
            return $slot->getUser() !== null;
        });
    }

    /**
     * @param int           $currentSlotIndex
     * @param callable|null $filter
     * @return int
     */
    public function getNextSlotIndex(int $currentSlotIndex, callable $filter = null): int
    {
        if (!isset($this->slots[$currentSlotIndex])) {
            throw new InvalidArgumentException('Unknown slotIndex ' . $currentSlotIndex);
        }

        $originalSlotIndex = $currentSlotIndex;
        for ($i = 0; $i < count($this->slots); $i++) {
            $currentSlotIndex = ($currentSlotIndex + 1) % count($this->slots);

            if ($this->slots[$currentSlotIndex]->getUser() === null) {
                continue;
            }
            if (is_callable($filter) && !$filter($this->slots[$currentSlotIndex])) {
                continue;
            }

            return $currentSlotIndex;
        }

        throw new LogicException('Could not find next slot index after slot ' . $originalSlotIndex);
    }
}