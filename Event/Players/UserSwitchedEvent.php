<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Players;

use Bywulf\GameCentralStation\Dto\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserSwitchedEvent
 * @package ByWulf\GameCentralStation\Event\Players
 */
class UserSwitchedEvent extends Event
{
    /**
     * @var int
     */
    private $oldSlotIndex;

    /**
     * @var int
     */
    private $newSlotIndex;

    /**
     * @var User
     */
    private $user;

    /**
     * @param int       $oldSlotIndex
     * @param int       $newSlotIndex
     * @param User|null $user
     */
    public function __construct(int $oldSlotIndex, int $newSlotIndex, ?User $user)
    {
        $this->oldSlotIndex = $oldSlotIndex;
        $this->newSlotIndex = $newSlotIndex;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getOldSlotIndex(): int
    {
        return $this->oldSlotIndex;
    }

    /**
     * @return int
     */
    public function getNewSlotIndex(): int
    {
        return $this->newSlotIndex;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

}