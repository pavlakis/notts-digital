<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Adapter;


interface AdapterInterface
{
    /**
     * @param $group
     * @return mixed
     */
    public function fetch($group);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @return string
     */
    public function getGroupName();
}