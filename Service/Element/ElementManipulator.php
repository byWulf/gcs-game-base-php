<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Service\Element;

use ByWulf\GameCentralStation\Element\ElementInterface;
use ByWulf\GameCentralStation\Exception\ElementException;
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
}