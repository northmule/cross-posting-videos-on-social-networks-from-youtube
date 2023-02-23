<?php

declare(strict_types=1);

namespace Coderun\Container\Exception;

use OutOfBoundsException;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ServiceNotFoundException
 *
 * @package Coderun\Container\Exception
 */
class ServiceNotFoundException extends OutOfBoundsException implements
    ExceptionInterface,
    NotFoundExceptionInterface
{
}
