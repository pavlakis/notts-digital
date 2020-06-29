<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Adapter;


interface AdapterInterface
{
    /**
     * @param string $group
     * @return mixed
     */
    public function fetch(string $group);

    /**
     * @return string
     */
    public function getGroupName(): string;

    /**
     * @return string
     */
    public function getGroupDescription(): string;

    /**
     * @return string
     */
    public function getGroupPhoto(): string;

    /**
     * @return \Iterator|array
     */
    public function getEventEntityCollection();
}