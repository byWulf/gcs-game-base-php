<?php
declare(strict_types=1);

namespace Bywulf\GameCentralStation\Model;

/**
 * Class User
 */
class User
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return User
     */
    public static function createFromData($data): User
    {
        return new User($data);
    }
}