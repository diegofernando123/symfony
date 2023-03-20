<?php namespace Yiin\RocketChat\API;

use Yiin\RocketChat\Client;
use GuzzleHttp\RequestOptions;

class Users
{
    private $client = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Creates new Rocket.Chat user
     * https://rocket.chat/docs/developer-guides/rest-api/users/create/
     */
    public function create($username, $password, $name, $email, $optionalArguments = array())
    {
        $response = $this->client->requestWithAuth(
            'POST', 'users.create', array(
            RequestOptions::JSON => array_merge(array(
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => $password
            ), $optionalArguments)
        ));

        return $response;
    }

    /**
     * Create a user authentication token.
     * https://rocket.chat/docs/developer-guides/rest-api/users/createtoken/
     * 
     * @param $identifier string 'userId' or 'username'.
     * @param $value string id or username of the user.
     */
    public function createToken($identifier, $value)
    {
        $response = $this->client->requestWithAuth(
            'POST', 'users.createToken', array(
            RequestOptions::JSON => array(
                $identifier => $value
            )
        ));

        return $response;
    }

    /**
     * Deletes an existing user.
     * https://rocket.chat/docs/developer-guides/rest-api/users/delete/
     */
    public function delete($id)
    {
        $this->client->requestWithAuth(
            'POST', 'users.delete', array(
            RequestOptions::JSON => array(
                'userId' => $id
            )
        ));

        return true;
    }

    /**
     * Gets the URL for a user's avatar.
     * https://rocket.chat/docs/developer-guides/rest-api/users/getavatar/
     * 
     * @param $identifier string 'userId' or 'username'.
     * @param $value string id or username of the user.
     */
    public function getAvatar($identifier, $value)
    {
        $response = $this->client->request(
            'GET', 'users.getAvatar', array(
            'query' => array(
                $identifier => $value
            )
        ));

        return $response;
    }

    /**
     * Sets Active status of the user.
     * https://rocket.chat/docs/developer-guides/rest-api/users/setactivestatus/
     * 
     * @param $userId string id or username of the user.
     */
    public function setActive($userId)
    {
        $response = $this->client->requestWithAuth(
            'POST', 'users.setActiveStatus', array(
            RequestOptions::JSON => array(
                'activeStatus' => true,
                'userId' => $userId
            )
        ));

        return $response;
    }

    /**
     * Gets the online presence of the a user.
     * https://rocket.chat/docs/developer-guides/rest-api/users/getpresence/
     * 
     * @param $optionalIdentifier string 'userId' or 'username'.
     * @param $optionalValue string id or username of the user.
     */
    public function getPresence($optionalIdentifier = null, $optionalValue = null)
    {
        $response = $this->client->requestWithAuth(
            'GET', 'users.getPresence', array(
            'query' => $optionalIdentifier && $optionalValue ? array(
                $optionalIdentifier => $optionalValue
            ) : array()
        ));

        return $response;
    }

    /**
     * Gets the online presence of the a user.
     * https://rocket.chat/docs/developer-guides/rest-api/users/get-preferences/
     */
    public function getPreferences()
    {
        $response = $this->client->requestWithAuth(
            'GET', 'users.getPreferences'
        );

        return $response;
    }

    /**
     * Gets a user's information, limited to the caller's permissions.
     * https://rocket.chat/docs/developer-guides/rest-api/users/info/
     */
    public function info($identifier, $value)
    {
        $response = $this->client->requestWithAuth(
            'GET', 'users.info', array(
            'query' => array(
                $identifier => $value
            )
        ));

        return $response;
    }

    /**
     * All of the users and their information, limited to permissions.
     * https://rocket.chat/docs/developer-guides/rest-api/users/list/
     */
    public function userlist($optionalFields = null, $optionalQuery = null)
    {
        $response = $this->client->requestWithAuth(
            'GET', 'users.list', array(
            'query' => array_filter(array(
                'fields' => $optionalFields,
                'query' => $optionalQuery
            ))
        ));

        return $response;
    }

    /**
     * 
     * 
     */
    public function register($username, $password, $name, $email, $optionalSecretURL = null)
    {
        $response = $this->client->request(
            'POST', 'users.register', array(
            RequestOptions::JSON => array_filter(array(
                'username' => $username,
                'pass' => $password,
                'name' => $name,
                'email' => $email,
                'secretURL' => $optionalSecretURL
            ))
        ));

        return $response;
    }

    /**
     * All of the users and their information, limited to permissions.
     * https://rocket.chat/docs/developer-guides/rest-api/users/resetavatar/
     */
    public function resetAvatar($type, $value)
    {
        $response = $this->client->requestWithAuth(
            'POST', 'users.resetAvatar', array(
            RequestOptions::JSON => array(
                $type => $value
            )
        ));

        return $response;
    }

    /**
     * Set a user's avatar
     * https://rocket.chat/docs/developer-guides/rest-api/users/setavatar/
     */
    public function setAvatarFile($avatar)
    {
    	
        $response = $this->client->requestWithAuth(
            'POST', 'users.setAvatar', array(
            RequestOptions::MULTIPART => array(
            	array(
            		'name' => 'image',
            		'contents' => fopen($avatar, 'r')
            	)
            )
        ));

        return $response;
    }

    /**
     * Set a user's avatar
     * https://rocket.chat/docs/developer-guides/rest-api/users/setavatar/
     */
    public function setAvatar($avatarUrl, $optionalIdentifier = null, $optionalValue = null)
    {
        $response = $this->client->requestWithAuth(
            'POST', 'users.setAvatar', array(
            RequestOptions::JSON => array(
                'avatar' => $avatarUrl
            ) + ($optionalIdentifier && $optionalValue) ? array(
                $optionalIdentifier => $optionalValue
            ) : array()
        ));

        return $response;
    }

    /**
     * Update an existing user.
     * https://rocket.chat/docs/developer-guides/rest-api/users/update/
     */
    public function update($userId, $data = array())
    {
        $response = $this->client->requestWithAuth(
            'POST', 'users.update', array(
            RequestOptions::JSON => array(
                'userId' => $userId,
                'data' => $data
            )
        ));

        return $response;
    }
}