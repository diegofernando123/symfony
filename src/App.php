<?php 

use AppBundle\Helpers\ConfigHelper;
use AppBundle\Helpers\BlockHelper;
use AppBundle\Helpers\DateHelper;
use AppBundle\Helpers\AppHelper;

class App {

	private $connection = null;
	private $container = null;
	private $mailer = null;
	private $db_type = "mysql";
	private $doctrine = null;
	private $secret = null;
	private $rootDir = null;
	private $user = null;
	private $security_context = null;

	public function init($container) {
		date_default_timezone_set("Etc/UTC");

		$this->container = $container;
		$this->secret = $container->getParameter('secret');
		$this->mailer = $container->get('mailer');
		$this->doctrine = $container->get('doctrine');

		$this->db_type = "pdo_mysql";
//		$this->db_type = $container->getParameter('doctrine.dbal.driver');

		$this->security_context = $container->get("security.token_storage");

/*
		if($this->db_type == "pdo_mysql") {
			$this->connection = $container->get('database_connection');
			$this->connection->query("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
			$this->connection->query("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
			$this->connection->query("SET time_zone='+00:00'");
		}
*/

		if($container->has("templating.engine.php")) {
			$container->get("templating.engine.php")->addHelpers(
					array(
							new AppHelper(),
							new BlockHelper(),
							new ConfigHelper(),
							new DateHelper()
					)
			);
		} else {
			$container->get("debug.templating.engine.php")->addHelpers(
					array(
							new AppHelper(),
							new BlockHelper(),
							new ConfigHelper(),
							new DateHelper()
					)
			);
		}
	}

	public function isLogged() {
		$user = $this->security_context->getToken()->getUser();
		if($user == null || !is_object($user)) {
			return false;
		}
		return true;
	}

	private $active_menu = "home";

	public function setActiveMenu($menu) {
		$this->active_menu = $menu;
	}

	public function getActiveMenu() {

		$route = $this->getRequest()->attributes->get('_route');

		if($route == 'post_index') {
			$route = 'home';
		}

		if($route == 'user_show') {
			$route = 'network_index';
		}

		if($route == 'user_network') {
			$route = 'network_index';
		}

		return $route;
	}

	public function getUserId() {
		$user = $this->security_context->getToken()->getUser();
		if($user == null || !is_object($user)) {
			return null;
		}
		return $user->getId();
	}

	public function getUser() {

		$user = $this->security_context->getToken()->getUser();
		
		if($user == null || !is_object($user)) {
			return null;
		}

		return $this->doctrine->getManager()->getRepository("AppBundle:User")->find($user->getId());
	}

	public function getUserRoles() {
		$user = $this->security_context->getToken()->getUser();

		if($user == null || !is_object($user)) {
			return array();
		}

		return $user->getRoles();
	}

	public function setRootDir($rootDir) {
		$this->rootDir = substr($rootDir, 0, strlen($rootDir) - 4);
	}

	public function getRootDir() {
		return $this->rootDir;
	}
	
	public function getConnection() {
		return $this->connection;
	}

	public function getDoctrine() {
		return $this->doctrine;
	}

	public function getMailer() {
		return $this->mailer;
	}

	public function getLogger() {
		return $this->container->get("logger");
	}

	public function getSecret() {
		return $this->secret;
	}

	public function getRequest() {
		return $this->container->get("request_stack")->getMasterRequest();
	}

	public function getAuthToken() {
	//	$auth_token = \App::getInstance()->getSession()->get("rocket_chat_auth_token");
	//	if($auth_token == null) {

		$auth_token = \Yiin\RocketChat\Client::getInstance($this->getUser())
						->getAuthToken();

	//		\App::getInstance()->getSession()->set("rocket_chat_auth_token", $auth_token);
	//	}
		return $auth_token;
	}

	public function getSession() {
		return $this->getRequest()->getSession();
	}

	public function getContainer() {
		return $this->container;
	}

	private static $instance = null;

	public static function getTable($repository) {
		return App::getInstance()->getDoctrine()->getManager()->getRepository($repository);
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new App();
		}
		return self::$instance;
	}

}
