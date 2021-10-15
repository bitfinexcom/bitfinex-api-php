<?php

declare(strict_types=1);

namespace BFX;

/**
 * Communicates with v2 of the Bitfinex HTTP API
 */
class RESTv2
{
    protected $url;
    protected $apiKey;
    protected $apiSecret;
    protected $authToken;
    protected $company;
    protected $transform;
    protected $agent;
    protected $affCode;

    /**
     * RESTv2 constructor.
     *
     * @param string $apiKey - API key
     * @param string $apiSecret - API secret
     * @param string $authToken - optional auth option
     * @param string $apiUrl - endpoint URL
     * @param boolean $transform
     * @param [string] $affCode - affiliate code to be applied to all orders
     * @param string $company
     * @param [string] $agent - optional user agent
     */
    public function __construct(
        $affCode = '',
        $apiKey = '',
        $apiSecret = '',
        $authToken = '',
        $company = '',
        $apiUrl = '',
        $transform = false,
        $agent = ''
    ) {
        $this->url = $apiUrl;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->authToken =  $authToken;
        $this->company = $company;
        $this->transform = $transform;
        $this->agent = $agent;
        $this->affCode = $affCode;

        if ($this->agent) {
            $this->client->setUserAgent($this->agent);
        }
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->url,
            'timeout' => 3.0,
        ]);
    }

    /**
     * @param string $path    - Api endpoint
     * @param mixed  $payload - Request body
     * @param [mixed] $transformer - Response transform function
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function makeAuthRequest(string $path, $payload, $transformer = null)
    {
        if ((!$this->apiKey || !$this->apiSecret) && !$this->authToken) {
            throw new \Exception('missing api key or secret');
        }

        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

        $nonce = (string) (time() * 1000 * 1000);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'bfx-nonce' => $nonce,
            'bfx-apikey' => $this->apiKey,
            'bfx-token' => $this->authToken,
        ];

        $response = $this->client->post($path, [
            'headers' => $headers,
            'body' => $bodyJson,
        ]);

        $response = $response->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * @param string $path - Api endpoint
     * @param [mixed] $transformer - Response transform function
     *
     * @return mixed
     */
    public function makePublicRequest(string $path, $body, $transformer = null)
    {
        $response = $this->client->get($path);

        $response = $response->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * @param string $path    - Api endpoint
     * @param [mixed] $transformer - Response transform function
     *
     * @return mixed
     */
    public function makePublicPostRequest(string $path, $body, $transformer = null)
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

    /**
     * @param $data - data
     * @param [mixed] $transformer - Response transform function
     */
    public function response ($data, $transformer) {
        try {
            $res = ($this->transform) ? $this->doTransform($data, $transformer): $data;
            return $this->$res;
        } catch (\Throwable $ex) {
            return $this->$ex;
        }
    }

    public function doTransform ($data, $transformer)
    {
        if (isClass($transformer)) {
            return $this->classTransform($data, $transformer);
        } else {
            return $data;
        }

    }
/**
 * @param $data - data
 * @param RESTv2 - class
 * @private
 */
    public function classTransform ($data) {
        if (!$data || $data === 0) return [];
        if (!$this->transform) return $data;

        if (is_array($data[0])) {
            return $data = array_map($row, new RESTv2($row, $this));
        }

        return new RESTv2($data, $this);
      }
}
