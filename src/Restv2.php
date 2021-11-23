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

    public function __construct(
        string $apiKey = '',
        string $apiSecret = '',
        string $authToken = '',
        string $apiUrl = 'https://api.bitfinex.com',
        string $company = '',
        bool $transform = false,
        ?string $affCode = null,
        ?string $agent = null,
        ?Client $client = null
    ) {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->authToken =  $authToken;
        $this->company = $company;
        $this->transform = $transform;
        $this->agent = $agent;
        $this->affCode = $affCode;

        if ($client === null) {
            $this->client = new Client([
                'base_uri' => $this->apiUrl,
                'timeout' => 3.0,
            ]);
        } else {
            $this->client = $client;
        }

        if ($this->agent) {
            $this->client->setUserAgent($this->agent);
        }
    }

    /**
     * @param string $path    - Api endpoint
     * @param mixed $payload - Request body
     * @param mixed $transformer - Response transform function
     * @param string $path - path
     * @return mixed
     *
     * @throws \Exception
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
     * @param string $path - Api endpoint
     * @param mixed  $payload - Request body
     * @param mixed $transformer - Response transform function
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
     * @param string $path - Api endpoint
     * @param mixed $transformer - Response transform function
     * @param mixed $body - Request body
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
     * @param object $data - data
     * @param mixed $transformer - Response transform function
     */
    protected function response($data, $transformer)
    {
        return ($this->transform) ? $this->doTransform($data, $transformer) : $data;
    }

    /**
     * @param object $data - data
     * @param mixed $transformer - Response transform function
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
     * @param object $data - data
     * @param mixed $transformer - Response transform function
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
     */
    public function status()
    {
        return $this->makePublicRequest('v2/platform/status');
    }

    /**
     * @see https://docs.bitfinex.com/reference#rest-auth-info-user
     */
    public function userInfo()
    {
        return $this->makeAuthRequest('/auth/r/info/user', [], UserInfo::class);
    }

    /**
     * @param string $ccy - i.e. ETH
     * @param numeric $start - query start
     * @param numeric $end - query end
     * @param numeric $limit - query limit, default 25
     * @see https://docs.bitfinex.com/v2/reference#movements
     */
    public function movements($ccy = null, $start = null, $end, $limit = 25)
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
     * @param $category - category
     * @param string $ccy - i.e. ETH
     * @param numeric $start - query start
     * @param numeric $end - query end
     * @param numeric $limit - query limit, default 25
     * @see https://docs.bitfinex.com/v2/reference#ledgers
     */
    public function ledgers($category = null, $ccy = null, $start = null, $end, $limit = 25)
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
     * Fetch the permissions of the key or token being used to generate this request
     */
    public function keyPermissions()
    {
        return $this->makeAuthRequest('/auth/r/permissions', [], AuthPermission::class);
    }

    /**
     * @param object $opts - options
     * @param numeric $opts [ttl] - time-to-live in seconds
     * @param string $opts [scope] - scope of the token
     * @param string $opts [caps] - token caps/permissions
     * @param boolean $opts [writePermission] - token write permission
     * @param string $opts [_cust_ip] - user ip address
     * @return mixed
     * @throws \Exception
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
     * @param object $params - parameters
     * @param string $params [wallet]  - wallet i.e exchange, margin
     * @param string $params [method]  - protocol method i.e bitcoin, tetherus
     * @param numeric $params [opRenew] - if 1 then generates a new address
     */
    public function getDepositAddress($params)
    {
        $params['op_renew'] = $params['opRenew'];
        return $this->makeAuthRequest('/auth/w/deposit/address', $params, Notification::class);
    }

    /**
     * @param object $params - parameters
     * @param string $params [wallet]  - wallet i.e exchange, margin
     * @param string $params [method]  - protocol method i.e bitcoin, tetherus
     * @param numeric $params [amount]  - amount to withdraw
     * @param string $params [address] - destination address
     */
    public function withdraw($params)
    {
        return $this->makeAuthRequest('/auth/w/withdraw', $params, Notification::class);
    }

    /**
     * @param object $params                                - invoice parameters
     * @param string $params [amount]                       - invoice amount in currency
     * @param string $params [currency]                     - invoice currency, currently supported: USD
     * @param string $params [payCurrencies]                - currencies in which invoice accepts the payments, supported
     *                                                 values are: BTC, ETH, UST-ETH, LNX
     * @param numeric $params [duration]                     - optional, invoice expire time in seconds, minimal duration
     *                                                 is 5 mins and maximal duration is 24 hours.
     *                                                 Default value is 15 minutes
     * @param string $params [orderId]                      - reference order identifier in merchant's platform
     * @param string $params [webhook]                      - the endpoint that will be called once the payment is
     *                                                 completed or expired
     * @param string $params [redirectUrl]                  - merchant redirect URL, this one is used in UI to redirect
     *                                                 customer to merchant's site once the payment is completed
     *                                                 or expired
     * @param object $params [customerInfo]                 - informatiturn $this->response(json_decode(on related to customer
     *                                                 against who the invoice is issued
     * @param string $params [customerInfo.nationality]     - customer's nationality, alpha2 code or full country name
     *                                                 (alpha2 preffered)
     * @param string $params [customerInfo.residCountry]    - customer's residential country, alpha2 code or
     *                                                 full country name (alpha2 preffered)
     * @param string $params [customerInfo.residState]      - optional, customer's residential state/province
     * @param string $params [customerInfo.residCity]       - customer's residential city/town
     * @param string $params [customerInfo.residZipCode]    - customer's residential zip code/postal code
     * @param string $params [customerInfo.residStreet]     - customer's residential street address
     * @param string $params [customerInfo.residBuildingNo] - optional, customer's residential building number/name
     * @param string $params [customerInfo.fullName]        - customer's full name
     * @param string $params [customerInfo.email]           - customer's email address
     * @see https://docs.bitfinex.com/reference#submit-invoice
     */
    public function payInvoiceCreate($params)
    {
        return $this->makeAuthRequest('/auth/w/ext/pay/invoice/create', $params);
    }

    /**
     * @param object $params - query parameters
     * @param string $params [id]    - unique invoice identifier
     * @param numeric $params [start] - millisecond start time
     * @param numeric $params [end]   - millisecond end time
     * @param numeric $params [limit] - number of records (Max 100), default 10
     * @see https://docs.bitfinex.com/reference#invoice-list
     */
    public function payInvoiceList($params)
    {
        return $this->makeAuthRequest('/auth/r/ext/pay/invoices', $params);
    }
}
