<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Element;

/**
 * Interface ElementEventInterface
 */
interface ElementEventInterface
{
    /**
     * @return int
     */
    public function getSlotIndex(): int;
}