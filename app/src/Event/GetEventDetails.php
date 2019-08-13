<?php

namespace NottsDigital\Event;

use Zend\Diactoros\Response\JsonResponse;
use Pimple\Container;

class GetEventDetails
{
    /**
     * @var array
     */
    private $groups;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    public function __construct(array $groups, EventFactory $eventFactory)
    {
        $this->groups = $groups;
        $this->eventFactory = $eventFactory;
    }

    public function getEvent(array $params): JsonResponse
    {
        if (!$this->hasGroup($params)) {
            return $this->defaultResponse();
        }

        $group = $params['group'];

        try {
            $event = $this->eventFactory->createFromEventType($this->getEventType($group))->getByGroup($group);

            return new JsonResponse(
                $event->toArray(),
                200,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (\InvalidArgumentException $e) {
            return $this->defaultResponse();
        }
    }

    private function defaultResponse(): JsonResponse
    {
        return new JsonResponse([
            'group'     => '',
            'subject'   => '',
            'description'   => '',
            'date_time' => '',
            'location'  => '',
            'event_url' => '',
            'iso_date' => '',
            'next_event' => []
        ]);
    }

    private function hasGroup( array $params): bool
    {
        return array_key_exists('group', $params);
    }

    private function getEventType(string $group)
    {
        foreach ($this->groups as $type => $events) {
            if (array_key_exists($group, $events)) {
                return $type;
            }
        }
    }
}
