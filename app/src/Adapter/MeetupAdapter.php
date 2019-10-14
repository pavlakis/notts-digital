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
    NottsDigital\Event\GroupInterface,
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
     * @var GroupInterface
     */
    protected $groupInfo;

    /**
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
     * @return mixed
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
     * @param string $group
     */
    protected function loadEventInfo($group): void
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
     * @param string $group
     */
    protected function loadGroupInfo($group): void
    {
        $groupUrlName = $this->config[$group]['group_urlname'];

        try {

            $groupInfo = $this->meetupRequest->fetchGroupInfo($groupUrlName);

            $this->groupInfo = new NullGroupInfo();
            if (!empty($groupInfo)) {

                $groupPhoto = isset($groupInfo['group_photo']) ? $groupInfo['group_photo']['highres_link'] : '';
                $this->groupInfo = new GroupInfo($groupInfo['name'], $groupInfo['description'], $groupPhoto);
            }
        } catch (\Exception $e) {
            $this->groupInfo = new NullGroupInfo();
        }
    }

    /**
     * @param array $events
     * @param string $nameMatch
     *
     * @return array
     */
    protected function getByNameStringMatch($events, $nameMatch): array
    {
        $nameMatch = $this->normaliseName($nameMatch);
        foreach ($events as $event) {
            $eventName = $this->normaliseName($event['name']);
            if (strpos($eventName, $nameMatch) !== false) {
                return $event;
            }
        }
        return [];
    }

    /**
     * @param string $name
     * @return string
     */
    protected function normaliseName($name): string
    {
        return (string) preg_replace('/\s*/', '',strtolower($name));
    }

    
    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupInfo->getGroupName();
    }

    /**
     * @return string
     */
    public function getGroupDescription(): string
    {
        return $this->groupInfo->getGroupDescription();
    }

    /**
     * @return string
     */
    public function getGroupPhoto(): string
    {
        return $this->groupInfo->getGroupPhoto();
    }

    /**
     * @return EventEntityCollection
     */
    public function getEventEntityCollection(): EventEntityCollection
    {
        return $this->eventEntityCollection;
    }
}
