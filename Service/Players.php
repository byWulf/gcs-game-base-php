<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service;

use Bywulf\GameCentralStation\Dto\Slot;
use ByWulf\GameCentralStation\Event\Backend as BackendEvent;
use ByWulf\GameCentralStation\Event\Players as PlayersEvent;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\Internal;
use ByWulf\GameCentralStation\Service\Slot\SlotManipulator;
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
     * @throws BackendCommunicatorException
     */
    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->slotManipulator = new SlotManipulator();

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
     * @param $callback
     */
    public function onJoin($callback): void
    {
        $this->eventDispatcher->addListener(PlayersEvent\UserJoinedEvent::class, $callback);
    }

    /**
     * @param $callback
     */
    public function onSwitch($callback): void
    {
        $this->eventDispatcher->addListener(PlayersEvent\UserSwitchedEvent::class, $callback);
    }

    /**
     * @param $callback
     */
    public function onLeave($callback): void
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
}