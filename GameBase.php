<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation;

use ByWulf\GameCentralStation\Event\Backend\Match\MatchAuthedEvent;
use ByWulf\GameCentralStation\Exception\BackendCommunicatorException;
use Bywulf\GameCentralStation\Service\Elements;
use Bywulf\GameCentralStation\Service\Game;
use Bywulf\GameCentralStation\Service\Players;

/**
 * Class GameBase
 */
class GameBase
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @var Players
     */
    private $players;
    /**
     * @var Elements
     */
    private $elements;

    /**
     * @throws BackendCommunicatorException
     */
    private function __construct()
    {
        $this->game = new Game($this);
        $this->players = new Players($this);
        $this->elements = new Elements($this);
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @return Players
     */
    public function getPlayers(): Players
    {
        return $this->players;
    }

    /**
     * @return Elements
     */
    public function getElements(): Elements
    {
        return $this->elements;
    }

    /**
     * @param callable $callback
     */
    public static function init(callable $callback): void
    {
        try {
            $gameBase = new GameBase();
            Internal::getBackendCommunicator()->on(MatchAuthedEvent::class, function () use ($callback, $gameBase) {
                Helper::getLogger()->debug('Starting init callback...');
                $callback($gameBase);
            }, -200);
            Internal::getBackendCommunicator()->connect('127.0.0.1', 3702);
        } catch (BackendCommunicatorException $e) {
            Helper::getLogger()->error((string) $e);
        }
    }
}