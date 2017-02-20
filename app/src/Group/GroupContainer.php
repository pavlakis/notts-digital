<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Group;

use NottsDigital\Group\Exception\NotFoundException;
use Psr\Container\ContainerInterface;

final class GroupContainer implements ContainerInterface
{
    /**
     * @var array
     */
    private $groupContainer;

    /**
     * Mapping group -> event, if already accessed by
     * has() or get()
     * @var array
     */
    private $cachedMappings;

    public function __construct(array $groups)
    {
        $this->groupContainer = $groups;
    }

    /**
     * @param $group
     * @param $eventPlatform
     */
    private function map($group, $eventPlatform)
    {
        $this->cachedMappings[$group] = $eventPlatform;
    }

    /**
     * @param $group
     * @return string
     */
    private function getEventPlatformByGroup($group)
    {
        return $this->cachedMappings[$group];
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {

            throw new NotFoundException('Group not found.');
        }

        return $this->getEventPlatformByGroup($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        foreach ($this->groupContainer as $eventPlatform => $events) {
            if (array_key_exists($id, $events)) {
                $this->map($id, $eventPlatform);
                return true;
            }
        }

        return false;
    }
}