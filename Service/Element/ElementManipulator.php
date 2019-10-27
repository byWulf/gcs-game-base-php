<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Service\Element;

use ByWulf\GameCentralStation\Element\ElementInterface;
use ByWulf\GameCentralStation\Exception\ElementException;
use ByWulf\GameCentralStation\GameBase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ElementManipulator
 * @package ByWulf\GameCentralStation\Service\Element
 *
 * @internal
 */
class ElementManipulator
{
    /**
     * @param ElementInterface $element
     * @return EventDispatcherInterface
     * @throws ElementException
     */
    public function getEventDispatcher(ElementInterface $element): EventDispatcherInterface
    {
        try {
            $reflectionClass = new ReflectionClass(get_class($element));
            $reflectionProperty = $reflectionClass->getProperty('eventDispatcher');
            $reflectionProperty->setAccessible(true);
            $eventDispatcher = $reflectionProperty->getValue($element);
            $reflectionProperty->setAccessible(false);
        } catch (ReflectionException $e) {
            throw new ElementException('Element must extend from AbstractElement class.', 0, $e);
        }

        return $eventDispatcher;
    }

    /**
     * @param ElementInterface $element
     * @param GameBase         $gameBase
     * @throws ElementException
     */
    public function injectGameBase(ElementInterface $element, GameBase $gameBase): void
    {
        try {
            $reflectionClass = new ReflectionClass(get_class($element));
            $reflectionProperty = $reflectionClass->getProperty('gameBase');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($element, $gameBase);
            $reflectionProperty->setAccessible(false);
        } catch (ReflectionException $e) {
            throw new ElementException('Element must extend from AbstractElement class.', 0, $e);
        }
    }
}