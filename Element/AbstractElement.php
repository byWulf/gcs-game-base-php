<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Element;


use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class AbstractElement
 * @package ByWulf\GameCentralStation\Element
 */
abstract class AbstractElement
{
    private $eventDispatcher;

    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
    }
}