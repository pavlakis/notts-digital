<?php

namespace NottsDigital\Event;

use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

interface GetEventDetailsInterface
{
    public const DEFAULT_PAYLOAD = [
        'group' => '',
        'subject' => '',
        'description' => '',
        'date_time' => '',
        'location' => '',
        'event_url' => '',
        'iso_date' => '',
        'next_event' => [],
    ];

    /**
     * @param ServerRequestInterface $request
     *
     * @return JsonResponse
     */
    public function getEvent(ServerRequestInterface $request): JsonResponse;
}
