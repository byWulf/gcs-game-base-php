<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Service;

use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use ByWulf\GameCentralStation\Exception\EventException;
use ByWulf\GameCentralStation\Helper;
use Bywulf\GameCentralStation\Service\Event\BackendEventCreator;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class BackendCommunicator
 * @package Bywulf\GameCentralStation\Service
 *
 * @internal
 */
class BackendCommunicator
{
    public const COMMAND_MATCH_AUTH = 'match.auth';

    /**
     * @var ConnectionInterface|null
     */
    private $connection;

    /**
     * @var BackendEventCreator
     */
    private $eventCreator;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct()
    {
        $this->eventCreator = new BackendEventCreator();
        $this->eventDispatcher = new EventDispatcher();
    }

    /**
     * @param string $ip
     * @param int    $port
     */
    public function connect(string $ip, int $port): void
    {
        $loop = Factory::create();
        $connector = new TcpConnector($loop);

        Helper::getLogger()->debug('Connecting...');
        $connector->connect($ip . ':' . $port)->then(function (ConnectionInterface $connection) use ($loop) {
            $this->connection = $connection;
            Helper::getLogger()->info('Connected to server!');

            $this->listenForMessages();
        });

        $loop->run();
    }
    
    private function listenForMessages(): void
    {
        $buffer = '';
        $this->connection->on('data', function(string $chunk) use (&$buffer): void {
            $buffer .= $chunk;
            if (strpos($buffer, "\0") !== false) {
                $messages = explode("\0", $buffer);

                for ($i = 0; $i < count($messages) - 1; $i++) {
                    $this->handleMessage($messages[$i]);
                }

                // Last message is incomplete -> Make it the new buffer
                $buffer = $messages[count($messages) - 1];
            }
        });
    }

    /**
     * @param string $message
     * @throws BackendCommunicatorException
     * @throws EventException
     */
    private function handleMessage(string $message): void
    {
        Helper::getLogger()->debug('Got message: ' . $message);

        $messageObject = json_decode($message, true);
        if (!isset($messageObject['action']) || !is_string($messageObject['action'])) {
            throw new BackendCommunicatorException('Got invalid message from server (action missing or not a string): ' . $message);
        }

        $event = $this->eventCreator->createEvent($messageObject['action'], $messageObject['data'] ?? null);

        $this->eventDispatcher->dispatch($event);
    }

    /**
     * @param string   $eventName
     * @param callable $callback
     * @param int      $priority
     * @throws BackendCommunicatorException
     */
    public function on(string $eventName, callable $callback, int $priority = 0): void
    {
        if (strpos($eventName, 'ByWulf\\GameCentralStation\\Event\\Backend\\') !== 0) {
            throw new BackendCommunicatorException('Event has to be a backend event.');
        }

        $this->eventDispatcher->addListener($eventName, $callback, $priority);
    }

    /**
     * @param string $action
     * @param mixed  $data
     * @throws BackendCommunicatorException
     */
    public function sendCommand(string $action, $data): void
    {
        if ($this->connection === null) {
            throw new BackendCommunicatorException('You must first connect to the server before you can send commands.');
        }

        $command = json_encode(['action' => $action, 'data' => $data]);
        Helper::getLogger()->debug('Sending command: ' . $command);
        $this->connection->write($command . "\0");
    }
}