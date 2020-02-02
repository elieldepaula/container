<?php

declare(strict_types=1);

namespace Elieldepaula\Container\Exception;

use Psr\Container\ContainerExceptionInterface as PsrContainerExceptionInterface;

/**
 * Container exceptions are thrown by the container when it cannot behave as it
 * has been requested to.
 *
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
class ContainerException extends \Exception implements PsrContainerExceptionInterface {}