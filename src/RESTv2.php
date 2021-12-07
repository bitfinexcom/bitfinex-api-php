<?php

declare(strict_types=1);

namespace BFX;

use BFX\Models\AuthPermission;
use BFX\Models\LedgerEntry;
use BFX\Models\Movement;
use BFX\Models\Notification;
use BFX\Models\UserInfo;
use GuzzleHttp\Client;

/**
 * Communicates with v2 of the Bitfinex HTTP API
 */
class RESTv2
{
    protected Client $client;
    protected string $apiUrl;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $authToken;
    protected string $company;
    protected bool $transform;
    protected ?string $agent;
    protected ?string $affCode;

    /**
     * @param array $params - Constructor params
     *                        [
     *                            'apiKey' => string,
     *                            'apiSecret' => string,
     *                            'authToken' => string,
     *                            'apiUrl' => string,
     *                            'company' => string,
     *                            'transform' => bool,
     *                            'affCode' => string,
     *                            'agent' => string,
     *                            'client' => Client
     *                        ]
     */
    public function __construct($params)
    {
        $this->apiUrl = isset($params['apiUrl']) ? $params['apiUrl'] : 'https://api.bitfinex.com';
        $this->apiKey = isset($params['apiKey']) ? $params['apiKey'] : '';
        $this->apiSecret = isset($params['apiSecret']) ? $params['apiSecret'] : '';
        $this->authToken = isset($params['authToken']) ? $params['authToken'] : '';
        $this->company = isset($params['company']) ? $params['company'] : '';
        $this->transform = isset($params['transform']) ? $params['transform'] : false;
        $this->affCode = isset($params['affCode']) ? $params['affCode'] : null;
        $this->agent = isset($params['agent']) ? $params['agent'] : null;
        $this->client = isset($params['client']) ? $params['client'] : null;

        if ($this->client === null) {
            $this->client = new Client([
                'base_uri' => $this->apiUrl,
                'timeout' => 3.0,
            ]);
        }

        if ($this->agent) {
            $this->client->setUserAgent($this->agent);
        }
    }

    /**
     * @param string $path - path
     * @param array $payload - payload
     * @param callable|string $transformer - model class or function
     *
     * @return mixed
     */
    public function makeAuthRequest(string $path, $payload, $transformer = null)
    {
        if ((!$this->apiKey || !$this->apiSecret) && !$this->authToken) {
            throw new \Exception('missing api key or secret');
        }

        $path = '/v2'.$path;

        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->authToken) {
            $headers['bfx-token'] = $this->authToken;
        } else {
            $nonce = (string) (time() * 1000 * 1000);
            $signature = "/api{$path}{$nonce}{$bodyJson}";

            $sig = hash_hmac('sha384', $signature, $this->apiSecret);
            $headers['bfx-nonce'] = $nonce;
            $headers['bfx-apikey'] = $this->apiKey;
            $headers['bfx-signature'] = $sig;
        }

        $response = $this->client->post($path, [
            'headers' => $headers,
            'body' => $bodyJson,
        ]);

        $response = $response->getBody()->getContents();

        return $this->response(json_decode($response), $transformer);
    }

    /**
     * @param string $path - path
     * @param callable|string $transformer - model class or function
     *
     * @return mixed
     */
    public function makePublicRequest(string $path, $transformer = null)
    {
        $response = $this->client->get($path);

        $response = $response->getBody()->getContents();

        return $this->response(json_decode($response), $transformer);
    }

    /**
     * @param string $path - path
     * @param array $body - payload
     * @param callable|string $transformer - model class or function
     *
     * @return mixed
     */
    public function makePublicPostRequest(string $path, $body, $transformer = null)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        $response = $this->client->post($path, [
            'headers' => $headers,
            'body' => $bodyJson,
        ]);

        $response = $response->getBody()->getContents();

        return $this->response(json_decode($response), $transformer);
    }

    /**
     * @param mixed $data - data
     * @param callable|string $transformer - model class or function
     *
     * @return mixed
     */
    protected function response($data, $transformer)
    {
        return ($this->transform) ? $this->doTransform($data, $transformer) : $data;
    }

    /**
     * @param mixed $data - data
     * @param callable|string $transformer - model class or function
     *
     * @return mixed
     */
    protected function doTransform($data, $transformer)
    {
        if ($transformer !== null && class_exists($transformer)) {
            return $this->classTransform($data, $transformer);
        } elseif (is_callable($transformer)) {
            return $transformer($data);
        } else {
            return $data;
        }
    }

    /**
     * @param mixed $data - data
     * @param string $transformer - model class or function
     *
     * @return mixed
     */
    protected function classTransform($data, $transformer)
    {
        if (!$data || $data === 0) {
            return [];
        }
        if (!$this->transform) {
            return $data;
        }

        if (is_array($data[0])) {
            return array_map(function ($row) use ($transformer) {
                return $transformer::{'unserialize'}($row);
            }, $data);
        }

        return $transformer::{'unserialize'}($data);
    }

    /**
     * @see https://docs.bitfinex.com/v2/reference#rest-public-platform-status
     *
     * @return int[]
     */
    public function status()
    {
        return $this->makePublicRequest('v2/platform/status');
    }

    /**
     * @see https://docs.bitfinex.com/reference#rest-auth-info-user
     *
     * @return array|UserInfo
     */
    public function userInfo()
    {
        return $this->makeAuthRequest('/auth/r/info/user', [], UserInfo::class);
    }

    /**
     * @see https://docs.bitfinex.com/v2/reference#movements
     *
     * @param string $ccy - i.e. ETH
     * @param int $start - query start
     * @param int $end - query end
     * @param int $limit - query limit, default 25
     *
     * @return array|Movement[]
     */
    public function movements($ccy = null, $start = null, $end = null, $limit = 25)
    {
        if (!$end) {
            $end = time() * 1000;
        }
        $url = $ccy ? "/auth/r/movements/$ccy/hist" : '/auth/r/movements/hist';

        $payload = [
            'start' => $start,
            'end' => $end,
            'limit' => $limit,
        ];

        return $this->makeAuthRequest($url, $payload, Movement::class);
    }

    /**
     * @see https://docs.bitfinex.com/v2/reference#ledgers
     *
     * @param string $category - category
     * @param string $ccy - i.e. ETH
     * @param int $start - query start
     * @param int $end - query end
     * @param int $limit - query limit, default 25
     *
     * @return array|LedgerEntry[]
     */
    public function ledgers($category = null, $ccy = null, $start = null, $end = null, $limit = 25)
    {
        if (!$end) {
            $end = time() * 1000;
        }

        $url = $ccy ? "/auth/r/ledgers/$ccy/hist" : '/auth/r/ledgers/hist';

        $payload = [
            'start' => $start,
            'end' => $end,
            'limit' => $limit,
            'category' => $category,
        ];
        return $this->makeAuthRequest($url, $payload, LedgerEntry::class);
    }

    /**
     * @see https://docs.bitfinex.com/reference#key-permissions
     *
     * @return array|AuthPermission[]
     */
    public function keyPermissions()
    {
        return $this->makeAuthRequest('/auth/r/permissions', [], AuthPermission::class);
    }

    /**
     * @see https://docs.bitfinex.com/reference#generate-token
     *
     * @param array $opts - options, associative array:
     *                      [
     *                          'ttl' => int - optional, time-to-live in seconds
     *                          'scope' => string - scope of the token
     *                          'caps' => string - optional, token caps/permissions
     *                          'writePermission' => boolean - optional, token write permission
     *                          '_cust_ip' => string - optional, user ip address
     *                      ]
     *
     * @return string[]
     */
    public function generateToken($opts)
    {
        $params = array_filter(
            (array)$opts,
            function ($key) {
                return in_array($key, ['ttl', 'scope', 'caps', 'writePermission', '_cust_ip']);
            },
            ARRAY_FILTER_USE_KEY
        );
        $params = array_filter($params, function ($value) {
            return !is_null($value);
        });
        if (!$params['scope']) {
            throw new \Exception('scope param is required');
        }

        return $this->makeAuthRequest('/auth/w/token', $params);
    }

    /**
     * @see https://docs.bitfinex.com/reference#rest-auth-deposit-address
     *
     * @param array $params - parameters
     *                        [
     *                            'wallet' => string - wallet i.e exchange, margin
     *                            'method' => string - protocol method i.e bitcoin, tetherus
     *                            'opRenew' => int - if 1 then generates a new address
     *                        ]
     *
     * @return array|Notification
     */
    public function getDepositAddress($params)
    {
        $params['op_renew'] = $params['opRenew'];
        return $this->makeAuthRequest('/auth/w/deposit/address', $params, Notification::class);
    }

    /**
     * @see https://docs.bitfinex.com/reference#rest-auth-withdraw
     *
     * @param object $params - parameters
     *                         [
     *                             'wallet' => string - wallet i.e exchange, margin
     *                             'method' => string - protocol method i.e bitcoin, tetherus
     *                             'amount' => string|float - amount to withdraw
     *                             'address' => string - destination address
     *                         ]
     *
     * @return array|Notification
     */
    public function withdraw($params)
    {
        return $this->makeAuthRequest('/auth/w/withdraw', $params, Notification::class);
    }

    /**
     * @see https://docs.bitfinex.com/reference#submit-invoice
     *
     * @param array $params - invoice parameters
     *                        [
     *                            'amount' => string - invoice amount in currency
     *                            'currency' => string - invoice currency, currently supported: USD
     *                            'payCurrencies' => string - currencies in which invoice accepts the payments, supported values are: BTC, ETH, UST-ETH, LNX
     *                            'duration' => int - optional, invoice expire time in seconds, minimal duration is 5 mins and maximal duration is 24 hours. Default value is 15 minutes
     *                            'orderId' => string - reference order identifier in merchant's platform
     *                            'webhook' => string - the endpoint that will be called once the payment is completed or expired
     *                            'redirectUrl' => string - merchant redirect URL, this one is used in UI to redirect customer to merchant's site once the payment is completed or expired
     *                            'customerInfo' => array - informatiturn $this->response(json_decode(on related to customer against who the invoice is issued
     *                                                      [
     *                                                          'nationality' => string - customer's nationality, alpha2 code or full country name (alpha2 preffered)
     *                                                          'residCountry' => string - customer's residential country, alpha2 code or full country name (alpha2 preffered)
     *                                                          'residState' => string - optional, customer's residential state/province
     *                                                          'residCity' => string - customer's residential city/town
     *                                                          'residZipCode' => string - customer's residential zip code/postal code
     *                                                          'residStreet' => string - customer's residential street address
     *                                                          'residBuildingNo' => string - optional, customer's residential building number/name
     *                                                          'fullName' => string - customer's full name
     *                                                          'email' => string - customer's email address
     *                                                      ]
     *                        ]
     *
     * @return object
     */
    public function payInvoiceCreate($params)
    {
        return $this->makeAuthRequest('/auth/w/ext/pay/invoice/create', $params);
    }

    /**
     * @see https://docs.bitfinex.com/reference#invoice-list
     *
     * @param array $params - query parameters
     *                        [
     *                            'id' => string - unique invoice identifier
     *                            'start' => int - millisecond start time
     *                            'end' => int - millisecond end time
     *                            'limit' => int - number of records (Max 100), default 10
     *                        ]
     *
     * @return object[]
     */
    public function payInvoiceList($params)
    {
        return $this->makeAuthRequest('/auth/r/ext/pay/invoices', $params);
    }
}
