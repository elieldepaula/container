<?php

declare(strict_types=1);

namespace Elieldepaula\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * The container interface. This extends the interface defined by
 * PSR-11 to include methods for retrieving parameters.
 *
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Retrieve a parameter from the container.
     *
     * @param string $name The parameter name.
     * @return mixed The parameter.
     * @throws ContainerException On failure.
     */
    public function getParameter($name);

    /**
     * Check to see if the container has a parameter.
     *
     * @param string $name The parameter name.
     * @return bool True if the container has the parameter, false otherwise.
     */
    public function hasParameter($name);
}