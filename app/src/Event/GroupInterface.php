<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Group;


interface GroupInterface
{
    /**
     * @return string
     */
    public function getGroupName();

    /**
     * @return string
     */
    public function getGroupInfo();
}