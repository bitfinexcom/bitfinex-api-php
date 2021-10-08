<?php

declare(strict_types=1);

namespace BFX;

/**
 * Communicates with v2 of the Bitfinex HTTP API
 */
class RESTv2
{
    private $url;
    private $apiKey;
    private $apiSecret;
    private $authToken;
    private $company;
    private $transform;
    private $agent;
    private $affCode;

    /**
     * RESTv2 constructor.
     * @param null $affCode
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $authToken
     * @param string $company
     * @param string $API_URL
     * @param false $transform
     * @param null $agent
     */
    public function __construct($affCode = null, $apiKey = '', $apiSecret = '', $authToken = '', $company = '', $API_URL = '', $transform = false, $agent = null)
    {
        $this->url = $API_URL;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->authToken =  $authToken;
        $this->company = $company;
        $this->transform = $transform;
        $this->agent = $agent;
        $this->affCode = $affCode;

        $baseUrl = $this->url;
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $baseUrl,
            'timeout' => 3.0,
        ]);
    }

    /**
     * @param $path
     * @param $payload
     * @param $transformer
     * @return mixed
     * @throws \Exception
     */
    public function makeAuthRequest($path, $payload, $transformer)
    {
        if ((!$this->apiKey || !$this->apiSecret) && !$this->authToken) {
            throw new \Exception('missing api key or secret');
        }

        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

        $nonce = (string) (time() * 1000 * 1000);
        $signature = "{$path}{$nonce}{$bodyJson}";

        $sig = hash_hmac('sha384', $signature, $this->apiSecret);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'bfx-nonce' => $nonce,
            'bfx-apikey' => $this->apiKey,
            'bfx-signature' => $sig,
        ];

        $response = $this->client->post($path, [
            'headers' => $headers,
            'body' => $bodyJson,
        ]);

        $response = $response->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * @param $path
     * @param $transformer
     * @return mixed
     */
    public function makePublicRequest($path, $transformer)
    {
        $response = $this->client->get($path);

        $response = $response->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * @param $path
     * @param $body
     * @param $transformer
     * @return mixed
     */
    public function makePublicPostRequest($path, $body, $transformer)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $bodyJson = json_encode($body);
        $response = $this->client->post($path, [
            'headers' => $headers,
            'body' => $bodyJson,
        ]);

        $response = $response->getBody()->getContents();

        return json_decode($response);
    }
}
