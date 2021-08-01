<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ComplexPassword extends Constraint
{
	public $message = 'Пароль должен содержать: не менее 8 символов и не более 32 символов, только цифры, заглавные и строчные буквы латиницей';

	public function validatedBy()
	{
		return \get_class($this).'Validator';
	}

	public function getTargets()
	{
		return self::PROPERTY_CONSTRAINT;
	}
}
