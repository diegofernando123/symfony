<?php
namespace AppBundle\Helpers;

use Symfony\Component\Templating\Helper\Helper;

class AppHelper extends Helper
{
	public $javascripts = array();
	public $css = array();

	public function require_js($url) {
		$this->javascripts[] = $url;
	}

	public function require_css($url) {
		$this->css[] = $url;
	}

	public function isGranted($crential) {
		$role_credentials = array(
			'ROLE_USER' => array(
					'IS_AUTHENTICATED_REMEMBERED'
			)	
		);

		foreach(\App::getInstance()->getUserRoles() as $role) {
			if(isset($role_credentials[$role]) && in_array($crential, $role_credentials[$role])) {
				return true;
			}
		}

		return false;
	}

	public function isFriend($user_id) {
		return \App::getInstance()->getTable("AppBundle:Network")->isFriend(\App::getInstance()->getUserId(), $user_id);
	}

	public function isConnected($user_id) {
		return \App::getInstance()->getTable("AppBundle:Network")->hasConnection(\App::getInstance()->getUserId(), $user_id);
	}

	public function isMe($id) {
		return \App::getInstance()->getUserId() == $id;
	}
	
	public function getActiveMenu() {
		return \App::getInstance()->getActiveMenu();
	}

	public function getUserId() {
		return \App::getInstance()->getUserId();
	}

	public function getUser() {
		return \App::getInstance()->getUser();
	}

	public function isLogged() {
		return \App::getInstance()->isLogged();
	}

	public function authToken() {
		return \App::getInstance()->getAuthToken();
	}

	public function getName() {
		return "app";
	}
}
