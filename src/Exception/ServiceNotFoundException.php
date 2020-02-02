<?php

declare(strict_types=1);

namespace Elieldepaula\Container\Exception;

use Psr\Container\NotFoundExceptionInterface as PsrNotFoundException;

/**
 * The ServiceNotFoundException is thrown when the container is asked to provide
 * a service that has not been defined.
 *
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
class ServiceNotFoundException extends \Exception implements PsrNotFoundException {}