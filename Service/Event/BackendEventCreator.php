<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service\Event;

use ByWulf\GameCentralStation\Exception\EventException;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

/**
 * Class BackendEventCreator
 *
 * @internal
 */
class BackendEventCreator
{
    /**
     * @param string $action
     * @param mixed       $data
     * @return Event
     * @throws EventException
     */
    public function createEvent(string $action, $data): Event
    {
        $classname = $this->getEventClassFromAction($action);
        if (!class_exists($classname)) {
            throw new EventException('Could not create event from action: ' . $action);
        }

        try {
            return new $classname($data);
        } catch (Throwable $e) {
            throw new EventException('Could not create event "' . $classname . '" from data: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $action
     * @return string
     */
    private function getEventClassFromAction(string $action): string
    {
        $eventParts = explode('.', $action);
        $classname = 'ByWulf\\GameCentralStation\\Event\\Backend\\';
        for ($i = 0; $i < count($eventParts) - 1; $i++) {
            $classname .= ucfirst($eventParts[$i]) . '\\';
        }
        $classname .= ucfirst($eventParts[count($eventParts) - 1]);

        return $classname;
    }
}