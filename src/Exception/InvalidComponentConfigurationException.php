<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @codeCoverageIgnore
 */
class InvalidComponentConfigurationException extends \Exception
{
    public function __construct(private readonly ConstraintViolationList $violationList)
    {
        parent::__construct('TwigDocBundle: component configuration is invalid: '.$violationList);
    }

    public function getViolationList(): ConstraintViolationList
    {
        return $this->violationList;
    }
}
