<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;


use NottsDigital\Adapter\TitoAdapter;

class TitoEvent implements EventInterface
{
    /**
     * @var TitoAdapter
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $event = [];

    /**
     * @var string
     */
    protected $group;

    public function __construct(TitoAdapter $adapter)
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
        $this->group = $group;
        $this->event = $this->adapter->fetch($group);

        return $this->event;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        $dateStr = '';
        try {
            $dateStr = $this->event->text();
        } catch (\InvalidArgumentException $e) {

        }
        preg_match("/(\w+)(\s{1})(\d{1,2})([a-zA-z]{2}),\s{1}(\d{4})/", $dateStr, $date);

        if (!is_array($date) && empty($date)) {
            throw new \InvalidArgumentException('Date does not exist or format unknown.');
        }

        return \DateTime::createFromFormat('F jS\, Y', $date[0]);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->adapter->getBaseUrl() . $this->event->attr('href');
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->group;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $date = '';
        try {
            $date = $this->getDate()->format('l jS F Y');
        } catch (\InvalidArgumentException $e) {}

        return [
            'group'     => $this->getGroupName(),
            'date_time' => $date,
            'event_url' => $this->getUrl()
        ];
    }
}