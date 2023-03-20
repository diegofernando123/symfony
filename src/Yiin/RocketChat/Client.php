<?php namespace Yiin\RocketChat;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Rocket.chat REST API Client
 */
class Client
{
    /**
     * HTTP client that we use for communication with Rocket.chat REST API.
     * @var HttpClient
     */
    private $httpClient = null;

    /**
     * Currently authenticated user auth details.
     * @var array
     */
    public $auth = null;
    
    public static $instance = null;

    /**
     * @param string $apiURL URL of the API (e.g. 'http://localhost:3000/api/v1')
     */
    public function __construct($apiUrl = "https://tradetoshare.com:8080/api/v1")
    {
        $this->httpClient = new HttpClient([
            'base_uri' => trim($apiUrl, '/') . '/'
        ]);
    }

    public static function getInstance($user = null) {
    	if(self::$instance == null) {
    		self::$instance = new \Yiin\RocketChat\Client();

    		if($user == null) {
    			$user = \App::getInstance()->getUser();
    		}

    		self::$instance->login($user);
    	}
    	return self::$instance;
    }

    public function login($user) {
	    try {

    		$result = $this->loginAs(
    				"u" . $user->getId(),
    				$user->getSalt()
			);

/*
    		if($user->getAvatar()) {
    			$fname = \App::getInstance()->getRootDir() . '/web/bundles/framework/images/user/' .  $user->getAvatar();

    			$fname = \App::getInstance()->getRootDir() . '/web/bundles/framework/images/no_avatar.png';

    			$response = $this->usersAPI()->setAvatarFile(
    				$fname
    			);
    			

    		}
*/

	    } catch(\Exception $x) {

    		if($x->getCode() == 401) {
    			$result = $this->loginAs(
    					'admin',
    					'Maui73shake'
    			);

    			$result = $this->usersAPI()->create(
    					"u" . $user->getId(),
    					$user->getSalt(),
    					strlen(trim($user->getName())) == 0 ? substr($user->getEmail(), 0, strpos($user->getEmail(), '@')) : $user->getName(),
    					$user->getEmail(),
    					array(
    							'joinDefaultChannels' => false
    					)
    			);

/*
    			if($user->getAvatar()) {
    				$fname = \App::getInstance()->getRootDir() . '/web/bundles/framework/images/user/' .  $user->getAvatar();

    				$this->usersAPI()->setAvatarFile(
    						$fname
    				);
    			}
*/

    			$result = $this->loginAs(
    					"u" . $user->getId(),
    					$user->getSalt()
    			);
    		}
    	}
    	 
    }

    public function getAuthToken() {
    	return $this->auth->authToken;
    }

    /**
     * Login to API and authorize subsequent requests
     * as user we just logged in.
     */
    public function loginAs($username, $password)
    {
        $response = $this->request(
            'POST', 'login', [
            RequestOptions::JSON => [
                'username' => $username,
                'password' => $password
            ]
        ]);

        if (($response->status ? $response->status : 'error') === 'success' ) {
            $this->auth = $response->data;
            return true;
        } else {
            $this->auth = $response->data;
            return false;
        }
    }

    public function authenticateWith($authToken, $userId, Callable $callback)
    {
        $auth = $this->auth;

        $this->auth = new \stdClass;
        $this->auth->authToken = $authToken;
        $this->auth->userId = $userId;

        $ret = $callback();

        $this->auth = $auth;

        return $ret;
    }

    /**
     * REST API Authentication
     * https://rocket.chat/docs/developer-guides/rest-api/authentication/
     */
    public function authenticationAPI()
    {
        return new API\Authentication($this);
    }

    /**
     * REST API Users
     * https://rocket.chat/docs/developer-guides/rest-api/users/
     */
    public function usersAPI()
    {
        return new API\Users($this);
    }

    /**
     * REST API Users
     * https://rocket.chat/docs/developer-guides/rest-api/subscriptions/
     */
    public function subscriptionsAPI()
    {
    	return new API\Subscriptions($this);
    }
    
    /**
     * REST API Groups
     * https://rocket.chat/docs/developer-guides/rest-api/groups/
     */
    public function groupsAPI()
    {
        return new API\Groups($this);
    }

    /**
     * REST API Channels
     * https://rocket.chat/docs/developer-guides/rest-api/channels/
     */
    public function channelsAPI()
    {
        return new API\Channels($this);
    }

    /**
     * REST API Instant Messaging
     * https://rocket.chat/docs/developer-guides/rest-api/im/
     */
    public function imAPI()
    {
    	return new API\Im($this);
    }

    /**
     * REST API Settings
     * https://rocket.chat/docs/developer-guides/rest-api/settings/
     */
    public function settingsAPI()
    {
        return new API\Settings($this);
    }

    /**
     * Helper methods
     */
    public function request($type, $endpoint, $options = [])
    {
        $response = $this->httpClient->request(
            $type, $endpoint, $options
        );

        $responseBody = (string) $response->getBody();
        $data = json_decode($responseBody);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        return $responseBody;
    }

    public function requestWithAuth($type, $endpoint, $options = [])
    {
        if (!$this->auth) {
            return $this->request($type, $endpoint, $options);
        }

        return $this->request($type, $endpoint, array_merge($options, [
            'headers' => [
                'X-Auth-Token' => $this->auth->authToken,
                'X-User-Id' => $this->auth->userId
            ]
        ]));
    }

    protected function createExceptionFromResponse($response, $prefix)
    {
        if (!empty($response->error)) {
            return new \Exception("$prefix: {$response->error}");
        } else if (!empty($response->message)) {
            return new \Exception("$prefix: {$response->message}");
        } else if (!empty($response) && is_string($response)) {
            return new \Exception("$prefix: {$response}");
        } else {
            return new \Exception("$prefix: unknown error.");
        }
    }
}
