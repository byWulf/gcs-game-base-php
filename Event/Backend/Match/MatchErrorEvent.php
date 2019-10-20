<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Backend\Match;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class MatchErrorEvent
 *
 * @internal
 */
class MatchErrorEvent extends Event
{
    private $key;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->setKey($data);
    }

    /**
     * @param string $key
     * @return $this
     */
    private function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }
}