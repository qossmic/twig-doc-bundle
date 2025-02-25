<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Component;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @codeCoverageIgnore
 */
readonly class ComponentInvalid
{
    public function __construct(
        public ConstraintViolationList $violationList,
        public array $originalConfig
    ) {
    }
}
