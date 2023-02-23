<?php

declare(strict_types=1);

namespace Coderun\Container\Exception;

use RuntimeException;

/**
 * Class InvalidServiceException
 *
 * @package Coderun\Container\Exception
 */
class InvalidServiceException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param string $name
     *
     * @return InvalidServiceException
     */
    public static function invalidParameter(string $name): InvalidServiceException
    {
        return new self(sprintf('Invalid configuration parameter: %s', $name));
    }

    /**
     * @param string $class
     * @param  mixed  $service
     *
     * @return InvalidServiceException
     */
    public static function unsupportedType(string $class, $service): InvalidServiceException
    {
        return new self(sprintf('Unsupported type \'%s\' for %s', gettype($service), $class));
    }
}
