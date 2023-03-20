<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Constraint;

use Doctrine\Logic\UserTable;

class CheckConfirmPasswordValidator extends ConstraintValidator
{
	
	public function validate($value, Constraint $constraint) {
		
		$data = $this->context->getRoot()->getData();
		
		if(!isset($data['password']) ||
				!isset($data['password_confirm']) ||
				strlen($data['password']) == 0 ||
				strlen($data['password_confirm']) == 0
				) {
			return;
		}
		
		if(!(strcmp($data['password'], $data['password_confirm']) == 0)) {
			$this->context->addViolation($constraint->message);
		}
	}
}
