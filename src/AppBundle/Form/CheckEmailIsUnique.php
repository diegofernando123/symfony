<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\Constraint;

class CheckEmailIsUnique extends Constraint
{

	public $message = "User with this email already registered";
	
}
