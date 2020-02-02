<?php

declare(strict_types=1);

namespace Elieldepaula\Container;

use Elieldepaula\Container\Exception\ContainerException;
use Elieldepaula\Container\Exception\ParameterNotFoundException;
use Elieldepaula\Container\Exception\ServiceNotFoundException;
use Elieldepaula\Container\Reference\ParameterReference;
use Elieldepaula\Container\Reference\ServiceReference;

/**
 * A simple dependency injection container.
 *
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $services;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $serviceStore;

    private const CLASS_KEY = 'class';

    private const ARGUMENT_KEY = 'arguments';

    private const METHOD_KEY = 'arguments';

    /**
     * Constructor for the container.
     *
     * Entries into the $services array must be an associative array with a
     * 'class' key and an optional 'arguments' key. Where present the arguments
     * will be passed to the class constructor. If an argument is an instance of
     * ContainerService the argument will be replaced with the corresponding
     * service from the container before the class is instantiated. If an
     * argument is an instance of ContainerParameter the argument will be
     * replaced with the corresponding parameter from the container before the
     * class is instantiated.
     *
     * @param array $services   The service definitions.
     * @param array $parameters The parameter definitions.
     */
    public function __construct(array $services = [], array $parameters = [])
    {
        $this->services     = $services;
        $this->parameters   = $parameters;
        $this->serviceStore = [];
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new ServiceNotFoundException('Service not found: '.$name);
        }

        if (!isset($this->serviceStore[$name])) {
            $this->serviceStore[$name] = $this->createService($name);
        }

        return $this->serviceStore[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParameter($name)
    {
        $tokens  = explode('.', $name);
        $context = $this->parameters;

        while (null !== ($token = array_shift($tokens))) {
            if (!isset($context[$token])) {
                throw new ParameterNotFoundException('Parameter not found: '.$name);
            }

            $context = $context[$token];
        }

        return $context;
    }

    /**
     * {@inheritDoc}
     */
    public function hasParameter($name)
    {
        try {
            $this->getParameter($name);
        } catch (ParameterNotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Attempt to create a service.
     *
     * @param string $name The service name.
     * @return mixed The created service.
     * @throws ContainerException On failure.
     */
    private function createService($name)
    {

        $entry = &$this->services[$name];

        if (!is_array($entry) || !isset($entry[self::CLASS_KEY])) {
            throw new ContainerException($name.' service entry must be an array containing a \'class\' key');
        } elseif (!class_exists($entry[self::CLASS_KEY])) {
            throw new ContainerException($name.' service class does not exist: '.$entry[self::CLASS_KEY]);
        } elseif (isset($entry['lock'])) {
            throw new ContainerException($name.' contains circular reference');
        }

        $entry['lock'] = true;

        $arguments = isset($entry[self::ARGUMENT_KEY]) ? $this->resolveArguments($entry[self::ARGUMENT_KEY]) : [];

        $reflector = new \ReflectionClass($entry[self::CLASS_KEY]);
        $service = $reflector->newInstanceArgs($arguments);

        if (isset($entry['calls'])) {
            $this->initializeService($service, $name, $entry['calls']);
        }

        return $service;
    }

    /**
     * Resolve argument definitions into an array of arguments.
     *
     * @param array  $argumentDefinitions The service arguments definition.
     * @return array The service constructor arguments.
     * @throws ContainerException On failure.
     */
    private function resolveArguments(array $argumentDefinitions)
    {
        $arguments = [];

        foreach ($argumentDefinitions as $argumentDefinition) {
            if ($argumentDefinition instanceof ServiceReference) {
                $argumentServiceName = $argumentDefinition->getName();

                $arguments[] = $this->get($argumentServiceName);
            } elseif ($argumentDefinition instanceof ParameterReference) {
                $argumentParameterName = $argumentDefinition->getName();

                $arguments[] = $this->getParameter($argumentParameterName);
            } else {
                $arguments[] = $argumentDefinition;
            }
        }

        return $arguments;
    }

    /**
     * Initialize a service using the call definitions.
     *
     * @param object $service         The service.
     * @param string $name            The service name.
     * @param array  $callDefinitions The service calls definition.
     * @throws ContainerException On failure.
     */
    private function initializeService($service, $name, array $callDefinitions)
    {
        foreach ($callDefinitions as $callDefinition) {
            if (!is_array($callDefinition) || !isset($callDefinition[self::METHOD_KEY])) {
                throw new ContainerException($name.' service calls must be arrays containing a \'method\' key');
            } elseif (!is_callable([$service, $callDefinition[self::METHOD_KEY]])) {
                throw new ContainerException($name.' service asks for call to uncallable method: '.$callDefinition[self::METHOD_KEY]);
            }

            $arguments = isset($callDefinition[self::ARGUMENT_KEY]) ? $this->resolveArguments($callDefinition[self::ARGUMENT_KEY]) : [];

            call_user_func_array([$service, $callDefinition[self::METHOD_KEY]], $arguments);
        }
    }
}