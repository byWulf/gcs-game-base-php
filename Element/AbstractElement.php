<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Element;


use ByWulf\GameCentralStation\Event\Element\ElementEventInterface;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\GameBase;
use ByWulf\GameCentralStation\Internal;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class AbstractElement
 * @package ByWulf\GameCentralStation\Element
 */
abstract class AbstractElement implements ElementInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var GameBase
     */
    private $gameBase;

    /**
     * @var ElementInterface
     */
    private $parentElement;

    /**
     * @var mixed
     */
    private $parentData;

    /**
     * @var mixed
     */
    private $logicData;

    /**
     * @param string $type
     * @param string $id
     */
    public function __construct(string $type, string $id)
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLogicData()
    {
        return $this->logicData;
    }

    /**
     * @param mixed $logicData
     * @return AbstractElement
     */
    public function setLogicData($logicData): AbstractElement
    {
        $this->logicData = $logicData;
        return $this;
    }

    /**
     * @return ElementInterface
     */
    public function getParentElement(): ElementInterface
    {
        return $this->parentElement;
    }

    /**
     * @return mixed
     */
    public function getParentData()
    {
        return $this->parentData;
    }

    /**
     * @param string $eventName
     * @param array  $data
     * @throws BackendCommunicatorException
     */
    protected function sendEvent(string $eventName, array $data = []): void
    {
        $data['event'] = $eventName;
        $data['id'] = $this->id;

        Internal::getBackendCommunicator()->sendCommand('event', $data);
    }

    public function clearPermissions(): void
    {
        // Can be overwritten in implementations
    }

    /**
     * @param string   $eventClassname
     * @param callable $callback
     */
    protected function on(string $eventClassname, callable $callback): void
    {
        $this->eventDispatcher->addListener('method', function (ElementEventInterface $event) use ($eventClassname, $callback) {
            if (get_class($event) === $eventClassname) {
                $callback($event);
            }
        });
    }
}