<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;


class NullGroupInfo implements GroupInterface
{

    /**
     * @return string
     */
    public function getGroupName()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getGroupDescription()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getGroupPhoto()
    {
        return '';
    }
}