<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Event\Backend\Match;

use Bywulf\GameCentralStation\Model\Slot;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class MatchAuthedEvent
 *
 * @internal
 */
class MatchAuthedEvent extends Event
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @var Slot[]
     */
    private $slots;

    public function __construct($data)
    {
        $this->setSettings($data['settings']);
        $this->setSlots($data['slots']);
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return MatchAuthedEvent
     */
    private function setSettings(array $settings): MatchAuthedEvent
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return Slot[]
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * @param mixed[] $slots
     * @return MatchAuthedEvent
     */
    private function setSlots(array $slots): MatchAuthedEvent
    {
        $this->slots = [];
        foreach ($slots as $index => $slot) {
            $this->slots[$index] = Slot::createFromData($slot, $index);
        }

        return $this;
    }
}