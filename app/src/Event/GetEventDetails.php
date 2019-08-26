<?php

namespace NottsDigital\Event;

use NottsDigital\Event\Exception\EventDoesNotExistException,
    Zend\Diactoros\Response\JsonResponse,
    Psr\Log\LoggerInterface;

class GetEventDetails
{
    /**
     * @var array
     */
    private $groups;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    public function __construct(array $groups, LoggerInterface $logger, EventFactory $eventFactory)
    {
        $this->groups = $groups;
        $this->logger = $logger;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param array $params
     * @return JsonResponse
     */
    public function getEvent(array $params): JsonResponse
    {
        if (!$this->hasGroup($params)) {
            return $this->getResponse($this->getDefaultPayload());
        }

        $group = $params['group'];

        try {
            $event = $this->eventFactory->createFromEventType($this->getEventType($group))->getByGroup($group);

            return $this->getResponse($event->toArray());
        } catch (EventDoesNotExistException $e) {
            $this->logger->warning('Group {group} not found.', [
                'group' => $group,
            ]);
            return $this->getResponse($this->getDefaultPayload());
        }
    }

    /**
     * @param array $payload
     * @return JsonResponse
     */
    private function getResponse(array $payload): JsonResponse
    {
        return new JsonResponse($payload, 200, [], JSON_PRETTY_PRINT);
    }

    private function hasGroup( array $params): bool
    {
        return array_key_exists('group', $params);
    }

    /**
     * @param string $group
     * @return string
     *
     * @throws EventDoesNotExistException
     */
    private function getEventType(string $group): string
    {
        foreach ($this->groups as $type => $events) {
            if (array_key_exists($group, $events)) {
                return $type;
            }
        }

        throw EventDoesNotExistException::forGroup($group);
    }

    /**
     * @return array
     */
    private function getDefaultPayload(): array
    {
        return [
            'group'     => '',
            'subject'   => '',
            'description'   => '',
            'date_time' => '',
            'location'  => '',
            'event_url' => '',
            'iso_date' => '',
            'next_event' => []
        ];
    }
}
