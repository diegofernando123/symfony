<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\Constraint;

class CheckLogin extends Constraint
{

	public $message = "User with this email already registered";
	public $message_not_active = "Sorry, your account has not been activated yes, please check your email and activate your account.";
	
}
