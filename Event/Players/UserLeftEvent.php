<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Players;

use Bywulf\GameCentralStation\Model\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserLeftEvent
 * @package ByWulf\GameCentralStation\Event\Players
 */
class UserLeftEvent extends Event
{
    /**
     * @var int
     */
    private $slotIndex;

    /**
     * @var User
     */
    private $user;

    /**
     * @param int       $slotIndex
     * @param User|null $user
     */
    public function __construct(int $slotIndex, ?User $user)
    {
        $this->slotIndex = $slotIndex;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getSlotIndex(): int
    {
        return $this->slotIndex;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

}