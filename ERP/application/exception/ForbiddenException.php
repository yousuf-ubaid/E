<?php

declare(strict_types = 1);

namespace App\Exception;

use InvalidArgumentException;

/**
 * A user is not allowed to perform the action
 *
 * @package App\Exception
 */
class ForbiddenException extends InvalidArgumentException
{
    /**
     * Factory default resource not found message
     *
     * @return static
     */
    public static function create(): self
    {
        return new self('Resource not found');
    }

}