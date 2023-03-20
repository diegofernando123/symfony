<?php namespace Yiin\RocketChat\API;

use Yiin\RocketChat\Client;
use GuzzleHttp\RequestOptions;

class Subscriptions
{
    private $client = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function markAsRead($rid) {
    	$response = $this->client->requestWithAuth(
    		'POST',
    		'subscriptions.read', [
            	RequestOptions::JSON => [
					'rid' => $rid
            	]
        	]
    	);
    	
    	return $response;
    }

    /**
     * Get all subscriptions.
     */
    public function get()
    {
        $response = $this->client->requestWithAuth(
            'GET', 
        	'subscriptions.get'
        );

        return $response;
    }
}