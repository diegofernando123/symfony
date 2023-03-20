<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\Constraint;

class CheckConfirmPassword extends Constraint
{

	public $message = "Password and Password (confirm) not equal";
	
}
