<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Model;

use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\Internal;

/**
 * Class Slot
 */
class Slot
{
    /**
     * @var User|null
     */
    private $user;
    /**
     * @var string
     */
    private $color;
    /**
     * @var bool
     */
    private $active;
    /**
     * @var int
     */
    private $points;

    /**
     * @var int
     */
    private $index;

    /**
     * @param User|null $user
     * @param string    $color
     * @param bool      $active
     * @param int       $points
     * @param int       $index
     */
    public function __construct(?User $user, string $color, bool $active, int $points, int $index)
    {
        $this->user = $user;
        $this->color = $color;
        $this->active = $active;
        $this->points = $points;
        $this->index = $index;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $index
     * @return Slot
     */
    public function setIndex(int $index): Slot
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Mark the player as active in the interface (independent to what actions he can perform)
     * @throws BackendCommunicatorException
     */
    public function setActive(): void
    {
        $this->active = true;

        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'slot.activeChanged',
            'slotIndex' => $this->index,
            'isActive' => $this->active
        ]);
    }

    /**
     * Mark the player as inactive in the interface (independent to what actions he can perform)
     * @throws BackendCommunicatorException
     */
    public function setInactive(): void
    {
        $this->active = false;

        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'slot.activeChanged',
            'slotIndex' => $this->index,
            'isActive' => $this->active
        ]);
    }

    /**
     * Add points to this player
     *
     * @param int $points
     * @throws BackendCommunicatorException
     */
    public function addPoints(int $points): void
    {
        $this->points += $points;

        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'slot.pointsChanged',
            'slotIndex' => $this->index,
            'points' => $this->points
        ]);
    }

    /**
     * Set the points of this player
     *
     * @param int $points
     * @throws BackendCommunicatorException
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;

        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'slot.pointsChanged',
            'slotIndex' => $this->index,
            'points' => $this->points
        ]);
    }

    /**
     * @param string $message
     * @throws BackendCommunicatorException
     */
    public function addNotification(string $message): void
    {
        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'notification.added',
            'text' => $message,
            'targetPlayers' => [$this->index]
        ]);
    }

    /**
     * @param array $data
     * @param int   $index
     * @return Slot
     */
    public static function createFromData(array $data, int $index): Slot
    {
        return new Slot(
            $data['user'] !== null ? User::createFromData($data['user']) : null,
            $data['color'],
            $data['active'],
            $data['points'],
            $index
        );
    }
}