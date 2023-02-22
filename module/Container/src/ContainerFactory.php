<?php

declare(strict_types=1);

namespace Coderun\Container;

use ArrayObject;
use Coderun\Container\Exception\InvalidServiceException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function is_numeric;
use function is_string;
use function class_exists;
use function method_exists;
use function count;
use function is_array;
use function is_callable;
use function sprintf;

/**
 * Class ContainerFactory
 *
 * @package Coderun\Container
 */
class ContainerFactory
{
    /**
     * Create container
     *
     * @param array $config
     * @return ContainerBuilder
     */
    public function __invoke(array $config): ContainerBuilder
    {
        $builder = new ContainerBuilder();
        $this->configure($builder, $config);
        return $builder;
    }

    /**
     * Configure service
     *
     * @param ContainerBuilder $builder
     * @param array            $config
     * @return void
     */
    private function configure(ContainerBuilder $builder, array $config): void
    {
        $builder->set('config', new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS));
        $dependencies = $config['dependencies'];
        foreach ($dependencies as $type => $services) {
            if ($type === 'services') {
                foreach ($services as $name => $service) {
                    $builder->set($name, $service);
                }
            }
            if ($type === Keys::INVOKABLES) {
                foreach ($services as $name => $service) {
                    if (is_numeric($name)) {
                        $builder->register($service, $service)->setLazy(true)->setPublic(true);
                        continue;
                    }
                    $builder->register($name, $service)->setLazy(true)->setPublic(true);
                }
            }
            if ($type === Keys::FACTORIES) {
                foreach ($services as $name => $service) {
                    $class = is_string($service) ? $service : $name;
                    $definition = $builder->register($name, $class);
                    $definition->setLazy(true);
                    $definition->setPublic(true);
                    $definition->setArguments([new Reference('service_container'), $name]);
                    if (
                        is_string($service) && class_exists($service)
                        && method_exists($service, '__invoke')
                    ) {
                        $definition->setFactory(new Reference($service));
                        $builder->set($service, new $service());
                        continue;
                    }
                    if (is_array($service) && count($service) == 2 && is_callable($service)) {
                        if (class_exists($service[0])) {
                            $definition->setFactory($service);
                            continue;
                        }
                    }
                    if (!is_string($service)) {
                        throw InvalidServiceException::unsupportedType($class, $service);
                    }
                }
            }
            if ($type === Keys::ALIASES) {
                foreach ($services as $name => $service) {
                    $builder->setAlias($name, $service)->setPublic(true);
                }
            }
            if ($type === Keys::REFLECTION) {
                foreach ($services as $service) {
                    $builder->autowire($service, $service)->setLazy(true)->setPublic(true);
                }
            }
        }
        $builder->compile();
    }
}
