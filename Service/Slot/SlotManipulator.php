<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Service\Slot;

use Bywulf\GameCentralStation\Model\Slot;
use Bywulf\GameCentralStation\Model\User;
use ReflectionClass;
use ReflectionException;

/**
 * Class SlotManipulator
 * @package ByWulf\GameCentralStation\Service\Slot
 *
 * @internal
 */
class SlotManipulator
{
    /**
     * @param Slot      $slot
     * @param User|null $user
     * @throws ReflectionException
     */
    public function setUser(Slot $slot, ?User $user): void
    {
        $reflectionClass = new ReflectionClass(Slot::class);
        $reflectionProperty = $reflectionClass->getProperty('user');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($slot, $user);
        $reflectionProperty->setAccessible(false);
    }
}