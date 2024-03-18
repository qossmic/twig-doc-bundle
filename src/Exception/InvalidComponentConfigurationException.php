<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @codeCoverageIgnore
 */
class InvalidComponentConfigurationException extends \Exception
{
    public function __construct(private readonly ConstraintViolationListInterface $violationList)
    {
        parent::__construct('TwigDocBundle: component configuration is invalid: '.$violationList);
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
