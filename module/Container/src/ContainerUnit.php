<?php

declare(strict_types=1);

namespace Coderun\Container;

use Coderun\Container\Exception\InvalidServiceException;
use Coderun\Container\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Psr\Container\ContainerInterface;

use function sprintf;
use function get_class;
use function is_object;
use function is_string;
use function class_exists;
use function method_exists;
use function count;
use function is_array;
use function is_callable;

/**
 * Class ContainerUnit
 *
 * @package Coderun\Container
 */
class ContainerUnit implements
    ContainerInterface,
    ContainerAwareInterface
{
    /** @var ContainerBuilder */
    protected ContainerBuilder $containerUnit;
    /** @var ContainerInterface */
    protected ContainerInterface $container;
    /** @var string */
    protected string $instanceOf;
    
    /**
     * ContainerUnit constructor.
     *
     * @param array              $config
     * @param ContainerInterface $container
     * @param string             $instanceOf
     */
    public function __construct(
        array $config,
        ContainerInterface $container,
        string $instanceOf
    ) {
        $this->container = $container;
        $this->instanceOf = $instanceOf;
        $this->containerUnit = new ContainerBuilder();
        $this->configure($this->containerUnit, $config);
    }
    
    /**
     * {@inheritDoc}
     * @template T
     * @param class-string<T> $id
     * @return T
     * @throws \Exception
     */
    public function get($id): object
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException(sprintf('Can\'t create service with name %s', $id));
        }
        if ($id === 'service_container') {
            return $this;
        }
        $service = $this->containerUnit->get($id);
        if (!is_object($service) || !$this->validate($service)) {
            throw new InvalidServiceException(
                sprintf('Service with name %s is invalid, expected %s interface', $id, $this->instanceOf)
            );
        }
        return $service;
    }
    
    /**
     * {@inheritDoc}
     *
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->containerUnit->has($id);
    }
    
    /**
     * Register service
     *
     * @param object $service
     * @return $this
     */
    public function addService(object $service): ContainerUnit
    {
        return $this->set(get_class($service), $service);
    }
    
    /**
     * Sets a service.
     *
     * @param string $id
     * @param object $service
     * @return $this
     */
    public function set(string $id, object $service): ContainerUnit
    {
        if (!$this->validate($service)) {
            throw new InvalidServiceException(
                sprintf('Expected %s interface, got %s', $this->instanceOf, get_class($service))
            );
        }
        $this->containerUnit->set($id, $service);
        return $this;
    }
    
    /**
     * Removes a service definition.
     *
     * @param string $id
     * @return $this
     */
    public function removeDefinition(string $id): ContainerUnit
    {
        $this->containerUnit->removeDefinition($id);
        return $this;
    }
    
    /**
     * {@inheritDoc}
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
    
    /**
     * Validate service
     *
     * @param object $service
     * @return bool
     */
    protected function validate(object $service): bool
    {
        return $service instanceof $this->instanceOf;
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
        $builder->set('config', new \ArrayObject($config, \ArrayObject::ARRAY_AS_PROPS));
        $builder->set(static::class, $this);
        $builder->register(static::class, static::class)->setLazy(true)->setPublic(true);
        $dependencies = $config['dependencies'];
        foreach ($dependencies as $type => $services) {
            if ($type === Keys::SERVICES) {
                foreach ($services as $name => $service) {
                    $builder->set($name, $service);
                }
            }
            if ($type === Keys::INVOKABLES) {
                foreach ($services as $name => $service) {
                    $builder->register($name, $service)->setLazy(true)->setPublic(true);
                }
            }
            if ($type === Keys::FACTORIES) {
                foreach ($services as $name => $service) {
                    $class = is_string($service) ? $service : $name;
                    $definition = $builder->register($name, $class);
                    $definition->setLazy(true);
                    $definition->setPublic(true);
                    $definition->setArguments([new Reference(static::class), $name]);
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
