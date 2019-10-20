<?php
declare(strict_types=1);

namespace ByWulf\GameCentralStation\Element;

/**
 * Interface ElementInterface
 * @package ByWulf\GameCentralStation\Element
 */
interface ElementInterface
{
    /**
     * Register a callback, when a specific method was called from the frontend aka player.
     *
     * @param string   $method
     * @param callable $callback
     */
    public function on(string $method, callable $callback): void;

    /**
     * Returns the unique id of the element.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns the type of the element.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Returns the parent element.
     *
     * @return ElementInterface|null
     */
    public function getParentElement(): ?ElementInterface;

    /**
     * Returns the parent data (aka where it belongs to the parent).
     *
     * @return mixed
     */
    public function getParentData();

    /**
     * Returns the array representation of the element.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Clears all permissions on this element, so no user can interact with it anymore.
     */
    public function clearPermissions(): void;
}