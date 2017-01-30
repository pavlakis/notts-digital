<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;


final class NullEventEntity implements EventEntityInterface
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return '';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'subject' => '',
            'description' => '',
            'date_time' => '',
            'location' => '',
            'event_url' => '',
            'iso_date' => ''
        ];
    }
}