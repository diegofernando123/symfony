<?php namespace Yiin\RocketChat\API;

use Yiin\RocketChat\Client;
use GuzzleHttp\RequestOptions;

class Im
{
    private $client = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Lists all of the direct messages the calling user has joined.
     */
    public function lists()
    {
        $response = $this->client->requestWithAuth(
            'GET', 'im.list', [
            'query' => [
            	'sort' => "{\"_updatedAt\":-1}"
            ]
        ]);

        return $response;
    }
}