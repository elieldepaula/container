<?php

declare(strict_types=1);

namespace Elieldepaula\Container\Reference;

/**
 * An abstract value object for a name that references some other value.
 *
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
abstract class AbstractReference
{
    /**
     * @var string
     */
    private $name;

    /**
     * Constructor for the container argument.
     *
     * @param string $name The service name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Retrieve the service name.
     *
     * @return string The service name.
     */
    public function getName()
    {
        return $this->name;
    }
}