<?php
namespace Fpp;

require_once 'Request.php';
require_once 'Response.php';
require_once 'RequestException.php';

use Fpp\RequestException;

/**
 * Http Client
 */
class Adapter{

    /**
     * The location of the cacert.pem file to use.
     * @var true or path, default current dir cacert.pem
     */
    private $cacertLocation = true;

    /**
     * whether use ssl for request
     */
    private $sslVerification = true;

    /**
     * The request public headers
     * @var array
     */
    private $headers = array();

    /**
     * The connection timeout time, which is 10 seconds by default
     * @var int
     */
    private $connectTimeout = 10;

    /**
     * The request timeout time, which is 120 seconds
     * @var int
     */
    private $socketTimeout = 120;

    /**
     * The curl options
     */
    private $curlOpts = array();

    /**
     * The Request class
     */
    private $requestClass = 'Fpp\Request';

    /**
     * The Response class
     */
    private $responseClass = 'Fpp\Response';

    /**
     * HttpClient
     * @param array $headers HTTP header
     */
    public function __construct($headers=array(), $connectTimeout=10000, $socketTimeout=120000){
        $this->headers = $headers;
        $this->connectTimeout = $connectTimeout;
        $this->socketTimeout = $socketTimeout;
    }

    /**
     * set the Response class
     */
    public function setResponseClass($response) {
        $this->responseClass = $response;
    }

    /**
     * set the Request class
     */
    public function setRequestClass($request) {
        $this->requestClass = $request;
    }

    /**
     * connect timeout
     * @param int $ms
     */
    public function setConnectionTimeoutInMillis($ms){
        $this->connectTimeout = $ms;
    }

    /**
     * response timeout
     * @param int $ms
     */
    public function setSocketTimeoutInMillis($ms){
        $this->socketTimeout = $ms;
    }

    /**
     * 配置
     * @param array $conf
     */
    public function setCurlOpts($conf){
        $this->curlOpts = $conf;
    }

    /**
     * set the location of the cacert path
     * @param mixed $location if the $location is true, set './cacert.pem'
     */
    public function setCacertLocation($location) {
        $this->cacertLocation = $location;
    }

    /**
     * @param bool $ssl the current adapter use ssl if or not
     */
    public function setSslVerification($ssl) {
        $this->sslVerification = $ssl;
    }

    /**
     * add request public headers
     * @param string $name the header name, if exists, replace
     * @param string $value the header value
     */
    public function addHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    /**
     * pre-process request handle, for example set curl options
     * @param resource $ch
     */
    public function prepare($ch){
        foreach($this->curlOpts as $key => $value){
            curl_setopt($ch, $key, $value);
        }
    }

    /**
     * @param  string $url
     * @param  array $param HTTP URL
     * @param  array $headers HTTP header
     * @return array
     */
    public function get($url, $params=array(), $headers=array()){
        return $this->request('GET', $url, $params, $headers);
    }

    /**
     * @param  string $url
     * @param  mixed $data HTTP POST BODY
     * @param  array $param HTTP URL
     * @param  array $headers HTTP header
     */
    public function post($url, $data=array(), $params=array(), $headers=array()){
        return $this->request('POST', $url, $data, $params, $headers);
    }

    public function request($method, $url, $data, $params=array(), $headers=array())
    {
        $request = new $this->requestClass($method, $url, $data, $params, $headers);
        $request->prepare();
        $response = $this->send($request);

        return $response;
    }

    public function send($request) {
        $ch = curl_init();
        $this->prepare($ch);

        $url = $request->url;
        $method = $request->method;

        $headers = array_merge($this->headers, $request->headers);
        $body = $request->body;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeaders($headers));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->socketTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);

        // Verification of the SSL cert
        if ($this->sslVerification && $this->isSslVerification($url)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        // chmod the file as 0755
        if ($this->cacertLocation === true) {
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        } elseif (is_string($this->cacertLocation)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->cacertLocation);
        }

        $content = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code === 0){
            throw new RequestException(curl_error($ch));
        }

        curl_close($ch);
        $response = $this->processResponse($content, $code);
        return $response;
    }

    /**
     * Process response before return
     */
    public function processResponse($content, $code)
    {
        $body = json_decode($content, true);
        return new $this->responseClass(null, $body, $code);
    }

    /**
     * 构造 header
     * @param  array $headers
     * @return array
     */
    private function buildHeaders($headers){
        $result = array();
        foreach($headers as $k => $v){
            $result[] = sprintf('%s: %s', $k, $v);
        }
        return $result;
    }

    /**
     * @param string $url the url send to
     * @return bool
     */
    private function isSslVerification($url) {
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        return $SSL;
    }
}
