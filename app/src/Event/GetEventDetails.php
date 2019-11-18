<?php

namespace NottsDigital\Event;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class GetEventDetails implements GetEventDetailsInterface
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

    /**
     * @param ServerRequestInterface $request
     *
     * @return JsonResponse
     */
    public function getEvent(ServerRequestInterface $request): JsonResponse
    {
        $params = $request->getQueryParams();
        if (!$this->hasGroup($params)) {
            return $this->getResponse($this->getDefaultPayload());
        }

        $group = $params['group'];

        try {
            $event = $this->eventFactory->createFromEventType($this->getEventType($group))->getByGroup($group);

            return $this->getResponse($event->toArray());
        } catch (\InvalidArgumentException $e) {
            return $this->getResponse($this->getDefaultPayload());
        }
    }

    /**
     * @param array $payload
     * @return JsonResponse
     */
    private function getResponse(array $payload): JsonResponse
    {
        return new JsonResponse(
            $payload,
            200,
            ['access-control-allow-origin' => '*'],
            JSON_PRETTY_PRINT
        );
    }

    private function hasGroup(array $params): bool
    {
        return array_key_exists('group', $params);
    }

    /**
     * @param string $group
     * @return string|null
     */
    private function getEventType(string $group): ?string
    {
        foreach ($this->groups as $type => $events) {
            if (array_key_exists($group, $events)) {
                return $type;
            }
        }
      
        return null;
    }

    /**
     * @return array
     */
    private function getDefaultPayload(): array
    {
        return GetEventDetailsInterface::DEFAULT_PAYLOAD;
    }
}
