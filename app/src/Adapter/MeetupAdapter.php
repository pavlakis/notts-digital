<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Adapter;


use NottsDigital\Event\EventEntityCollection,
    NottsDigital\Http\Request\MeetupRequest,
    NottsDigital\Event\NullEventEntity,
    NottsDigital\Event\NullGroupInfo,
    NottsDigital\Event\EventEntity,
    NottsDigital\Event\GroupInfo;

/**
 * Class MeetupAdapter
 * @package NottsDigital\Adapter
 */
class MeetupAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var MeetupRequest
     */
    protected $meetupRequest;

    /**
     * @var array
     */
    protected $groupConfig = [];

    /**
     * @var EventEntityCollection
     */
    protected $eventEntityCollection;

    /**
     * @var GroupInfo
     */
    protected $groupInfo;

    /**
     * MeetupAdapter constructor.
     * @param array $config
     * @param MeetupRequest $meetupRequest
     * @param EventEntityCollection $eventEntityCollection
     */
    public function __construct(
        array $config,
        MeetupRequest $meetupRequest,
        EventEntityCollection $eventEntityCollection
    )
    {
        $this->config = $config;
        $this->meetupRequest = $meetupRequest;
        $this->eventEntityCollection = $eventEntityCollection;
    }


    /**
     * @param string $group
     * @return array
     */
    public function fetch($group)
    {
        if (!isset($this->config[$group])) {
            return [];
        }

        $this->loadEventInfo($group);

        $this->loadGroupInfo($group);
    }

    /**
     * @param $group
     */
    protected function loadEventInfo($group)
    {
        $groupUrlName = $this->config[$group]['group_urlname'];

        try {

            $events = $this->meetupRequest->fetchEventInfo($groupUrlName);

            if (!isset($events['results']) || empty($events['results'])) {
                throw new \Exception('No events found.');
            }

            $this->groupConfig = $this->config[$group];

            if (isset($this->config[$group]['match']) && isset($this->config[$group]['match']['name'])) {
                $this->eventEntityCollection->add(
                    new EventEntity($this->getByNameStringMatch($events['results'], $this->config[$group]['match']['name']))
                );
            } else {

                $this->eventEntityCollection->add(new EventEntity($events['results'][0], $this->groupConfig));

                if (isset($events['results'][1])) {
                    $this->eventEntityCollection->add(new EventEntity($events['results'][1], $this->groupConfig));
                }
            }

        } catch (\Exception $e) {
            $this->eventEntityCollection->add(new NullEventEntity());
        }

    }

    /**
     * @param $group
     */
    protected function loadGroupInfo($group)
    {
        $groupUrlName = $this->config[$group]['group_urlname'];

        try {

            $groupInfo = $this->meetupRequest->fetchGroupInfo($groupUrlName);

            if (isset($groupInfo['results']) && !empty($groupInfo['results'])) {
                $groupInfo = $groupInfo['results'][0];

                $this->groupInfo = new GroupInfo($groupInfo['name'], $groupInfo['description'], $groupInfo['group_photo']['highres_link']);
            } else {
                $this->groupInfo = new NullGroupInfo();
            }

        } catch (\Exception $e) {
            $this->groupInfo = new NullGroupInfo();
        }
    }

    /**
     * @param $events
     * @param $nameMatch
     * @return bool
     */
    protected function getByNameStringMatch($events, $nameMatch)
    {
        foreach ($events as $event) {
            if (strpos($event['name'], $nameMatch) !== false) {
                return $event;
            }
        }
        return [];
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupInfo->getGroupName();
    }

    /**
     * @return string
     */
    public function getGroupDescription()
    {
        return $this->groupInfo->getGroupDescription();
    }

    /**
     * @return string
     */
    public function getGroupPhoto()
    {
        return $this->groupInfo->getGroupPhoto();
    }

    /**
     * @return array
     */
    public function getEventEntityCollection()
    {
        return $this->eventEntityCollection;
    }
}
