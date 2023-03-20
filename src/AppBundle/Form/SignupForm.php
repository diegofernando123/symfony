<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\Constraints\Choice;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;

use Symfony\Component\Form\FormBuilderInterface;

class SignupForm extends AbstractForm
{
	protected $vars = ['first_name', 'last_name', /*'phone',*/ 'email', 'password', 'password_confirm'];

	protected $first_name = null;
	protected $last_name = null;
/*	protected $phone = null; */
	protected $email = null;
	protected $password = null;
	protected $password_confirm = null;

	public function getFirstName() {
		return $this->first_name;
	}

	public function setFirstName($first_name) {
		$this->first_name = $first_name;
	}

	public function getLastName() {
		return $this->last_name;
	}

	public function setLastName($last_name) {
		$this->last_name = $last_name;
	}
/*
	public function getPhone() {
		return $this->phone;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
	}
*/
	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setPassword_confirm($password) {
		$this->password_confirm = $password;
	}

	public function getPassword_again() {
		return $this->password_confirm;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('first_name', "Symfony\Component\Form\Extension\Core\Type\TextType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
		$builder->add('last_name', "Symfony\Component\Form\Extension\Core\Type\TextType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
/*		$builder->add('phone', "Symfony\Component\Form\Extension\Core\Type\TextType", array('required' => false, 
				'attr' => array('autocomplete' => 'off'))); */
		$builder->add('email', "Symfony\Component\Form\Extension\Core\Type\EmailType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
		$builder->add('password', "Symfony\Component\Form\Extension\Core\Type\PasswordType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
		$builder->add('password_confirm', "Symfony\Component\Form\Extension\Core\Type\PasswordType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
	}

	public function getDefaultOptions(array $options)
	{
		$collectionConstraint = new Collection(array(
				'first_name' => array(
						new NotBlank(array('message' => 'First Name is required'))
				 ),
				'last_name' => array(
						new NotBlank(array('message' => 'Last Name is required'))
				 ),
				'email' => array(
						new NotBlank(array('message' => 'Email Address is required')),
						new Email(array('message' => 'Email Address is incorrect')),
						new CheckEmailIsUnique(array('message' => 'User with this email already registered')) 
				 ),
				'password' => new NotBlank(array('message' => 'Password is required')),
				'password_confirm' => array(
						new NotBlank(array('message' => 'Password is required')),
						new CheckConfirmPassword(array('message' => 'Both passwords should be the same'))
				)
		));
		
		return array(
				'constraints' => $collectionConstraint,
				'csrf_protection' => false
		);
	}

}
