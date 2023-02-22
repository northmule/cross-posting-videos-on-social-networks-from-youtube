<?php

declare(strict_types=1);

namespace Coderun\Container;

use Psr\Container\ContainerInterface;

/**
 * Interface ContainerAwareInterface
 *
 * @package Coderun\Container
 */
interface ContainerAwareInterface
{
    /**
     * Return container
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;
}
