<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service;

use ByWulf\GameCentralStation\Element\ElementInterface;
use ByWulf\GameCentralStation\Event\Backend\Match\MatchMethodEvent;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\Exception\ElementException;
use ByWulf\GameCentralStation\GameBase;
use ByWulf\GameCentralStation\Helper;
use ByWulf\GameCentralStation\Internal;
use ByWulf\GameCentralStation\Service\Element\ElementManipulator;
use Bywulf\GameCentralStation\Service\Event\ElementEventCreator;

/**
 * Class Elements
 * @package Bywulf\GameCentralStation\Service
 */
class Elements
{
    /**
     * @var ElementManipulator
     */
    private $elementManipulator;

    /**
     * @var ElementEventCreator
     */
    private $eventCreator;

    /**
     * @var ElementInterface[]
     */
    private $elements = [];
    /**
     * @var GameBase
     */
    private $gameBase;

    /**
     * @param GameBase $gameBase
     * @throws BackendCommunicatorException
     */
    public function __construct(GameBase $gameBase)
    {
        $this->elementManipulator = new ElementManipulator();
        $this->eventCreator = new ElementEventCreator();
        $this->gameBase = $gameBase;

        Internal::getBackendCommunicator()->on(MatchMethodEvent::class, function(MatchMethodEvent $event) {
            $element = $this->getElementById($event->getElementId());
            if ($element === null) {
                return;
            }

            try {
                $event = $this->eventCreator->createEvent($event->getMethod(), $event->getSlotIndex(), $event->getData());
                $this->elementManipulator->getEventDispatcher($element)->dispatch($event);
            } catch (ElementException $e) {
                Helper::getLogger()->error('Could not create event for method "' . $event->getMethod() . '".');
            }
        });
    }

    /**
     * @param string $id
     * @return ElementInterface|null
     */
    public function getElementById(string $id): ?ElementInterface
    {
        foreach ($this->elements as $element) {
            if ($element->getId() === $id) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Register an element, so it can be displayed to the users.
     *
     * @param ElementInterface $element
     * @throws BackendCommunicatorException
     */
    public function registerElement(ElementInterface $element): void
    {
        $this->elements[] = $element;

        Internal::getBackendCommunicator()->sendCommand('event', [
            'event' => 'element.added',
            'type' => $element->getType(),
            'parent' => [
                'id' => $element->getParentElement() !== null ? $element->getParentElement()->getId() : null,
                'data' => $element->getParentData(),
            ],
            'element' => $element->toArray()
        ]);
    }

    /**
     * Clears all permissions on all elements, so no user can interact with anything anymore.
     */
    public function clearAllPermissions(): void
    {
        foreach ($this->elements as $element) {
            $element->clearPermissions();
        }
    }
}