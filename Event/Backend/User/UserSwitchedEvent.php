<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Backend\User;

use Bywulf\GameCentralStation\Model\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserSwitchedEvent
 * @package ByWulf\GameCentralStation\Event\User
 *
 * @internal
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
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->setOldSlotIndex($data['oldSlotIndex']);
        $this->setNewSlotIndex($data['newSlotIndex']);
        $this->setUser($data['user']);
    }

    /**
     * @param int $oldSlotIndex
     * @return $this
     */
    private function setOldSlotIndex(int $oldSlotIndex): self
    {
        $this->oldSlotIndex = $oldSlotIndex;

        return $this;
    }

    /**
     * @param int $newSlotIndex
     * @return $this
     */
    private function setNewSlotIndex(int $newSlotIndex): self
    {
        $this->newSlotIndex = $newSlotIndex;

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