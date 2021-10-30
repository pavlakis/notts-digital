<?php
/**
 * Nottingham Digital events.
 *
 * @see      https://github.com/pavlakis/notts-digital
 *
 * @copyright Copyright (c) 2017 Antonios Pavlakis
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
     * Gets event.
     *
     * @param string $group
     *
     * @return Event
     */
    public function getByGroup($group)
    {
        $this->adapter->fetch($group);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $eventInfo = [
            'group' => $this->adapter->getGroupName(),
            'group_photo' => $this->adapter->getGroupPhoto(),
            'group_description' => $this->adapter->getGroupDescription(),
            'next_event' => [],
            'subject' => '',
            'description' => '',
            'date_time' => '',
            'location' => '',
            'event_url' => '',
            'iso_date' => '',
        ];

        foreach ($this->adapter->getEventEntityCollection() as $key => $eventEntity) {
            /* @var EventEntityInterface $eventEntity */
            if (0 === $key) {
                $eventInfo = array_merge($eventInfo, $eventEntity->toArray());
                continue;
            }

            if (1 === $key) {
                $eventInfo['next_event'] = $eventEntity->toArray();
            }
        }

        return $eventInfo;
    }
}
