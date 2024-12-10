<?php

declare(strict_types = 1);

namespace App\Exception;

use InvalidArgumentException;

/**
 * Service is unavailable
 *
 * @package App\Exception
 */
class ServiceUnavailableException extends InvalidArgumentException
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