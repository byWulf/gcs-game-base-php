<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Backend\Match;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class MatchMethodEvent
 *
 * @internal
 */
class MatchMethodEvent extends Event
{
    /**
     * @var string
     */
    private $elementId;

    /**
     * @var string
     */
    private $method;

    /**
     * @var int
     */
    private $slotIndex;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->setElementId($data['elementId']);
        $this->setMethod($data['method']);
        $this->setSlotIndex($data['slotIndex']);
        $this->setData($data['data']);
    }

    /**
     * @param string $elementId
     * @return $this
     */
    private function setElementId(string $elementId): self
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    private function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
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
     * @param $data
     * @return $this
     */
    private function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getSlotIndex(): int
    {
        return $this->slotIndex;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

}