<?php

declare(strict_types=1);

namespace Elieldepaula\Container\Exception;

/**
 * The ParameterNotFoundException is thrown when the container is asked to
 * provide a parameter that has not been defined.
 *
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
class ParameterNotFoundException extends \Exception {}