<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component;

use Symfony\Component\Validator\ConstraintViolationList;

class ComponentInvalid
{
    public function __construct(
        public readonly ConstraintViolationList $violationList,
        public readonly array $originalConfig
    ) {
    }
}
