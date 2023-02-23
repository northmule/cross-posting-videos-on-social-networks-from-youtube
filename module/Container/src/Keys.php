<?php

declare(strict_types=1);

namespace Coderun\Container;

/**
 * Class Keys
 *
 * @package Coderun\Container
 */
class Keys
{
    /** @var string  */
    public const INVOKABLES = 'invokables';
    /** @var string  */
    public const FACTORIES = 'factories';
    /** @var string  */
    public const ALIASES = 'aliases';
    /** @var string  */
    public const REFLECTION = 'reflection';
    /** @var string  */
    public const SERVICES = 'services';

    /**
     * @return array<int,string>
     */
    public function all(): array
    {
        return [
            self::INVOKABLES,
            self::FACTORIES,
            self::ALIASES,
            self::REFLECTION,
            self::SERVICES,
        ];
    }
}
