<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ComplexPasswordValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof ComplexPassword) {
			throw new UnexpectedTypeException($constraint, ComplexPassword::class);
		}

		if (null === $value || '' === $value) {
			return;
		}

		if (!is_string($value)) {
			throw new UnexpectedValueException($value, 'string');
		}

		if (!preg_match('/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]){8,32}/', $value)) {
			$this->context->buildViolation($constraint->message)
				->setParameter('{{ string }}', $value)
				->addViolation();
		}
	}
}
