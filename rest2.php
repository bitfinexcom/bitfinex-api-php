<?php
declare(strict_types = 1);


//$BASE_TIMEOUT = 15000;


/**
 * Parses response into notification object
 *
 * @param {object} data - notification
 * @returns {Notification} n
 * @private
 */
//function _takeResNotify (data) {
//    $notification = new Notification(data);
//  return notification;
//}

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
     * Instantiate a new REST v2 transport.
     *
     * @param {object} opts - options
     * @param {string} [opts.affCode] - affiliate code to be applied to all orders
     * @param {string} [opts.apiKey] - API key
     * @param {string} [opts.apiSecret] - API secret
     * @param {string} [opts.authToken] - optional auth option
     * @param {string} [opts.url] - endpoint URL
     * @param {boolean} [opts.transform] - default false
     * @param {object} [opts.agent] - optional node agent for connection (proxy)
     */


 public function __construct()
{
    $affCode = null;
    $apiKey = '';
    $apiSecret = '';
    $authToken = '';
    $company = '';
    $API_URL = 'https://api.bitfinex.com';
    $transform = false;
    $agent = null;

    $this->url = $this->$API_URL;
    $this->apiKey = $this->$apiKey;
    $this->apiSecret = $this->$apiSecret;
    $this->authToken = $this->$authToken;
    $this->company = $this->$company;
    $this->transform = $this->$transform;
    $this->agent = $this->$agent;
    $this->affCode = $this->$affCode;


    $baseUrl = $this->url;
    $this->client = new GuzzleHttp\Client([
        'base_uri' => $baseUrl,
        'timeout' => 3.0,
    ]);
}

    /**
     * @param {string} path - path
     * @param {Function} [cb] - callback
     * @param {object|Function} transformer - model class or function
     * @returns {Promise} p
     * @private
     */

    public function _makePublicRequest ($path, $cb, $transformer) {

    $BASE_TIMEOUT = 15000;
    $url = `${this->url}/v2${path}`;

    debug('GET %s', $url);

    return $rp([
      $url,
      'timeout' => $BASE_TIMEOUT,
      'agent' => $this->agent,
      'json' => true
    ]).then((data) => [
    return this->_response($data, $transformer, $cb);
    ])
  }

//    /**
//     * @param {object} data - data
//     * @param {object|Function} transformer - model class or function
//     * @param {Function} [cb] - callback
//     * @returns {object|object[]} finalData
//     * @private
//     */
//    public function _response (data, transformer, cb) {
//    try {
//        const res = (this._transform)
//            ? this._doTransform(data, transformer)
//            : data
//
//      return this._cb(null, res, cb)
//    } catch (e) {
//        return this._cb(e, null, cb)
//    }
//  }
}
