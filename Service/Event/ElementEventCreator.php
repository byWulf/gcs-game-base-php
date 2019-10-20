<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service\Event;

use ByWulf\GameCentralStation\Exception\EventException;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

/**
 * Class ElementEventCreator
 *
 * @internal
 */
class ElementEventCreator
{
    /**
     * @param string $method
     * @param int    $slotIndex
     * @param mixed  $data
     * @return Event
     * @throws EventException
     */
    public function createEvent(string $method, int $slotIndex, $data): Event
    {
        $classname = $this->getEventClassFromMethod($method);
        if (!class_exists($classname)) {
            throw new EventException('Could not create event from method: ' . $method);
        }

        try {
            return new $classname($slotIndex, $data);
        } catch (Throwable $e) {
            throw new EventException('Could not create event "' . $classname . '" from data: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $action
     * @return string
     */
    private function getEventClassFromMethod(string $action): string
    {
        $eventParts = explode('.', $action);
        $classname = 'ByWulf\\GameCentralStation\\Event\\Element\\';
        for ($i = 0; $i < count($eventParts) - 1; $i++) {
            $classname .= ucfirst($eventParts[$i]) . '\\';
        }
        $classname .= ucfirst($eventParts[count($eventParts) - 1]);

        return $classname;
    }
}