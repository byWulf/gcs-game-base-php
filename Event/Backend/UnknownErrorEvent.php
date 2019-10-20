<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Backend;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UnknownErrorEvent
 *
 * @internal
 */
class UnknownErrorEvent extends Event
{
    private $message;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->setMessage($data);
    }

    /**
     * @param string $message
     * @return $this
     */
    private function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}