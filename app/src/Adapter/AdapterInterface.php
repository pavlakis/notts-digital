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
    public function fetch($group);

    /**
     * @return string
     */
    public function getGroupName();

    /**
     * @return string
     */
    public function getGroupDescription();

    /**
     * @return string
     */
    public function getGroupPhoto();

    /**
     * @return \Iterator|array
     */
    public function getEventEntityCollection();
}