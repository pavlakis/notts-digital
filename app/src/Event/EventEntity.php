<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;


final class EventEntity implements EventEntityInterface
{
    /**
     * @var array
     */
    private $event;

    /**
     * @var array
     */
    private $config;

    /**
     * EventEntity constructor.
     * @param array $eventData
     * @param array $config
     */
    public function __construct(array $eventData = [], array $config = [])
    {
        $this->event = $eventData;
        $this->config = $config;
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
     * @return string
     */
    public function getDescription()
    {
        if (!isset($this->event['description'])) {
            return '';
        }

        return $this->event['description'];
    }

    /**
     * @return string|\DateTime
     * @throws \Exception
     */
    public function getDate()
    {
        if (!isset($this->event['time'])) {
            return '';
        }

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
        if (isset($this->config['link_to'])) {
            return $this->config['link_to'];
        }

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

        $address = '';
        if ($venue) {
            $venueDetails = ['name', 'address_1', 'city'];
            foreach ($venueDetails as $details) {
                if (isset($venue[$details])) {
                    if ('' !== \trim($address)) {
                        $address .= ', ';
                    }
                    $address .= $venue[$details];
                }
            }
        }

        return $address;
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
            $isoDate = $this->getDate()->format('c');
        }

        return [
            'subject' => $this->getTitle(),
            'description' => $this->getDescription(),
            'date_time' => $date,
            'location' => $this->getLocation(),
            'event_url' => $this->getUrl(),
            'iso_date' => $isoDate
        ];
    }
}