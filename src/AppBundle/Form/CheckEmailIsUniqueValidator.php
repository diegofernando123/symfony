<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Constraint;

use AppBundle\Repository\UserRepository;

class CheckEmailIsUniqueValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint) {
		if(count($this->context->getViolations()) == 0) {
			if(\App::getTable('AppBundle:User')->isExistsEmail($value)) {
				$this->context->addViolation($constraint->message);
			}
		}
		
	}
	
}
