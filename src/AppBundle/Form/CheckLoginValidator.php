<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Constraint;

use AppBundle\Repository\UserRepository;

class CheckLoginValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint) {
		if(count($this->context->getViolations()) == 0) {
			$data = $this->context->getRoot()->getData();

			if(!isset($data['password'])) {
				$data['password'] = '';
			}

			if(!\App::getTable('AppBundle:User')->checkCredentials($data)) {
				$this->context->addViolation($constraint->message);
			}
			if(!\App::getTable('AppBundle:User')->isActivated($data)) {
				$this->context->addViolation($constraint->message_not_active);
			}
		}
	}
}
