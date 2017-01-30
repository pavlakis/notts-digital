<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;


interface EventEntityInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

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
     * @return array
     */
    public function toArray();
}