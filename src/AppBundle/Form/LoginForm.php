<?php

namespace AppBundle\Form;

use Symfony\Component\Validator\Constraints\Choice;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;

use Symfony\Component\Form\FormBuilderInterface;

class LoginForm extends AbstractForm
{
	protected $vars = ['email', 'password'];

	protected $email = null;
	protected $password = null;

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPassword() {
		return $this->password;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('email', "Symfony\Component\Form\Extension\Core\Type\TextType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
		$builder->add('password', "Symfony\Component\Form\Extension\Core\Type\PasswordType", array('required' => false, 
				'attr' => array('autocomplete' => 'off')));
	}

	public function getDefaultOptions(array $options)
	{
		$collectionConstraint = new Collection(array(
				'email' => array(
						new NotBlank(array('message' => 'Email Address is required')),
				 ),
				'password' => array(
						new NotBlank(array('message' => 'Password is required')),
						new CheckLogin(array('message' => 'Login/password is incorrect')) 
				)
		));

		return array(
				'constraints' => $collectionConstraint,
				'csrf_protection' => false
		);
	}

}
