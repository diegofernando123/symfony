<?php

namespace AppBundle\Constraints;

use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Constraint;

use AppBundle\Repository\UserRepository;

class ExperienceEndValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint) {
	
		if(count($this->context->getViolations()) == 0) {
	
			if($this->context->getRoot()->getData()->getIsCurrent() == false) {
				if($value == null) {
					$this->context->addViolation($constraint->message);
				} else {
					if($this->context->getRoot()->getData()->getStart()->getTimestamp() >=  $this->context->getRoot()->getData()->getEnd()->getTimestamp()) {
						$this->context->addViolation($constraint->shouldBeLater);
					}
				}
			}
		
		}
	}
}
