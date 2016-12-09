<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace NottsDigital\Event;

use NottsDigital\Adapter\AdapterInterface;

class Event implements EventInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
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
        $this->adapter->fetch($group);

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->adapter->getTitle();
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->adapter->getDate();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->adapter->getUrl();
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->adapter->getLocation();
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->adapter->getGroupName();
    }

    /**
     * @return array
     */
    public function getGroupInfo()
    {
        return $this->adapter->getGroupInfo();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $date = '';
        $isoDate = '';

        if ($this->getDate() instanceof \DateTime) {
            $date = $this->getDate()->format('l jS F Y') . ' at ' . $this->getDate()->format('g:ia');
            $isoDate =  $this->getDate()->format('c');
        }

        return [
            'group'     => $this->getGroupName(),
            'subject'   => $this->getTitle(),
            'date_time' => $date,
            'location'  => $this->getLocation(),
            'event_url' => $this->getUrl(),
            'iso_date'  => $isoDate,
            'group_info' => $this->getGroupInfo()
        ];
    }
}