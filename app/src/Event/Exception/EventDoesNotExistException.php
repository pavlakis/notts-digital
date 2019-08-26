<?php

namespace NottsDigital\Event\Exception;

use NottsDigital\Exception\BaseException;

class EventDoesNotExistException extends BaseException
{
    public static function forGroup(?string $group): EventDoesNotExistException
    {
        return new self(
            sprintf('Group %s does not exist', (string)$group)
        );
    }
}
