<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Backend\User;

use Bywulf\GameCentralStation\Dto\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserLeftEvent
 * @package ByWulf\GameCentralStation\Event\User
 *
 * @internal
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
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->setSlotIndex($data['slotIndex']);
        $this->setUser($data['user']);
    }

    /**
     * @param int $slotIndex
     * @return $this
     */
    private function setSlotIndex(int $slotIndex): self
    {
        $this->slotIndex = $slotIndex;

        return $this;
    }

    /**
     * @param $user
     * @return $this
     */
    private function setUser($user): self
    {
        $this->user = User::createFromData($user);

        return $this;
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