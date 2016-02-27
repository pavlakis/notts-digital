<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;

use NottsDigital\Adapter\MeetupAdapter;

class MeetupEvent implements EventInterface
{
    /**
     * @var MeetupAdapter
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $event = [];

    public function __construct(MeetupAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Gets event
     *
     * @param string $group
     * @return array
     */
    public function getByGroup($group)
    {
        $this->event = $this->adapter->fetch($group);

        return $this->event;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if (!isset($this->event['name'])) {
            return '';
        }

        return $this->event['name'];
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        $time = $this->event['time'] / 1000;

        $date = new \DateTime();
        $date->setTimestamp($time);

        return $date;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!isset($this->event['event_url'])) {
            return '';
        }

        return $this->event['event_url'];
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        $venue = isset($this->event['venue']) ? $this->event['venue'] : '';

        if ($venue) {
            return $venue['name'] . ', ' . $venue['address_1'] . ', ' . $venue['city'];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        if(isset($this->event['group']) && isset($this->event['group']['name'])) {
            return $this->event['group']['name'];
        }

        return '';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'group'     => $this->getGroupName(),
            'subject'   => $this->getTitle(),
            'date_time' => $this->getDate()->format('l jS F Y') . ' at ' . $this->getDate()->format('g:ia'),
            'location'  => $this->getLocation(),
            'event_url' => $this->getUrl()
        ];
    }
}