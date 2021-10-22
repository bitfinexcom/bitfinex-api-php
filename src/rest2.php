<?php

declare(strict_types=1);

namespace BFX;

use BFX\models\AuthPermission;
use BFX\models\LedgerEntry;
use BFX\models\Movement;
use BFX\models\Notification;
use BFX\models\UserInfo;

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
     * @param string $path - Api endpoint
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
    public function response($data, $transformer)
    {
        try {
            $res = ($this->transform) ? $this->doTransform($data, $transformer) : $data;
            return $this->$res;
        } catch (\Throwable $ex) {
            return $this->$ex;
        }
    }


    /**
     * @param $data - data
     * @param [mixed] $transformer - Response transform function
     */
    public function doTransform($data, $transformer)
    {
        if ($transformer !== null) {
            return $this->classTransform($data, $transformer);
        } else {
            return $data;
        }
    }

    /**
     * @param $data - data
     * @param [mixed] $transformer - Response transform function
     */
    public function classTransform($data, $transformer)
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
        return $this->makePublicRequest('/platform/status');
    }

    /**
     * @see https://docs.bitfinex.com/reference#rest-auth-info-user
     */
    public function userInfo()
    {
        return $this->makePublicRequest('/auth/r/info/user', [], UserInfo::class);
    }

    /**
     * @param $ccy - i.e. ETH
     * @param $start - query start
     * @param $end - query end
     * @param $limit - query limit, default 25
     * @see https://docs.bitfinex.com/v2/reference#movements
     */
    public function movements($ccy, $start = null, $end, $limit = 25)
    {
        $end = date();
        $url = $ccy ? `/auth/r/movements/{$ccy}/hist` : '/auth/r/movements/hist';

        return $this->makeAuthRequest($url, [ $start, $end, $limit ], Movement::class);
    }

    /**
     * @param $category - category
     * @param $ccy - i.e. ETH
     * @param $start - query start
     * @param $end - query end
     * @param $limit - query limit, default 25
     * @see https://docs.bitfinex.com/v2/reference#ledgers
     */
    public function ledgers($category, $ccy, $start = null, $end, $limit = 25)
    {
        $end = date();

        $url = $ccy ? `/auth/r/ledgers/{$ccy}/hist` : '/auth/r/ledgers/hist';

        return $this->makeAuthRequest($url, [$start, $end, $limit, $category], LedgerEntry::class);
    }

    /**
     * Fetch the permissions of the key or token being used to generate this request
     */
    public function keyPermissions()
    {
        return $this->makeAuthRequest('/auth/r/permissions', [], AuthPermission::class);
    }

    /**
     * @param $opts - options
     *
     * @return mixed
     * @throws \Exception
     */
    public function generateToken($opts)
    {
        if (!$opts->scope) {
            throw new \Exception('missing api key or secret');
        }

        return $this->makeAuthRequest('/auth/w/token', $opts);
    }

    /**
     * @param $params - parameters
     * @return mixed
     * @throws \Exception
     */
    public function getDepositAddress($params)
    {
        $params->op_renew = $params->opRenew;
        return $this->makeAuthRequest('/auth/w/deposit/address', $params, Notification::class);
    }

    /**
     * @param $params - parameters
     * @return mixed
     * @throws \Exception
     */
    public function withdraw($params)
    {
        return $this->makeAuthRequest('/auth/w/withdraw', $params, Notification::class);
    }

    /**
     * @param $params - invoice parameters
     * @return mixed
     * @throws \Exception
     * @see https://docs.bitfinex.com/reference#submit-invoice
     */
    public function payInvoiceCreate($params)
    {
        return $this->makeAuthRequest('/auth/w/ext/pay/invoice/create', $params);
    }

    /**
     * @param $params - query parameters
     * @return mixed
     * @throws \Exception
     * @see https://docs.bitfinex.com/reference#invoice-list
     */
    public function payInvoiceList($params)
    {
        return $this->makeAuthRequest('/auth/r/ext/pay/invoices', $params);
    }
}
