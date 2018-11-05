<?php

namespace Fpp;

require_once 'lib/MimeTypes.php';
require_once 'lib/MultiPartForm.php';
require_once 'lib/Adapter.php';

/**
 * Class FppClient
 *
 * Face++ api client for sending request to cloud server.
 *
 * @package Fpp
 */

class FppClient
{
    // the server uri
    // in china is api-cn.faceplusplus.com, and in others is api-us.faceplusplus.com
    private $host;

    // the request key
    private $apiKey;

    // the request secret
    private $apiSecret;

    // establish connection timeout time
    private $connectTimeout;

    // the request timeout time, send request but not receive response in some time
    private $socketTimeout;

    private $sslVerification = true;

    private $curlOpts = array();

    // public headers
    private $headers = array();

    // the client commicumating to the face++ server
    private $adapter;

    // adapter class
    private $adapterClass = 'Fpp\Adapter';

    /**
     * Constructor
     *
     * Usage: $client = new FppClient(apiKey, apiSecret, host);
     *
     * @param string $apiKey The key you obtain from face++ web console
     * @param string $apiSecret The secret you obtain from face++ web console
     * @param string $hostname The domain name of the datacenter,For example: api-cn.faceplusplus.com
     * @throws Exception
     */
    public function __construct($apiKey, $apiSecret, $host, $connectTimeout=10000, $socketTimeout=120000)
    {
        $apiKey = trim($apiKey);
        $apiSecret = trim($apiSecret);
        $host = trim(trim($host), "/");

        if (empty($apiKey)) {
            throw new Exception("apiKey is empty");
        }
        if (empty($apiSecret)) {
            throw new Exception("apiSecret is empty");
        }
        if (empty($host)) {
            throw new Exception("host is empty");
        }

        $this->host = $host;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->connectTimeout = $connectTimeout;
        $this->socketTimeout = $socketTimeout;
    }

    /**
     * @param array $options The options for detect
     * @throws Exception \ RequestException
     */
    public function detectFace($options)
    {
        $path = '/facepp/v3/detect';
        return $this->request($path, $options);
    }

    public function compareFace($options)
    {
        $path = '/facepp/v3/compare';
        return $this->request($path, $options);
    }

    public function searchFace($options)
    {
        $path = '/facepp/v3/search';
        return $this->request($path, $options);
    }

    /**
     * create a faceset if outer_id not exists, else when
     * outer_id exists and force_merge is 0, return 400 error.
     */
    public function createFaceset($options)
    {
        $path = '/facepp/v3/faceset/create';
        return $this->request($path, $options);
    }

    /**
     * add one or more face to exists faceset
     */
    public function addFaceset($options) {
        $path = '/facepp/v3/faceset/addface';
        return $this->request($path, $options);
    }

    /**
     * remove one or more face of the faceset
     */
    public function removeFaceset($options) {
        $path = '/facepp/v3/faceset/removeface';
        return $this->request($path, $options);
    }

    /**
     * update faceset information
     */
    public function updateFaceset($options) {
        $path = '/facepp/v3/faceset/update';
        return $this->request($path, $options);
    }

    /**
     * get faceset information
     */
    public function getDetailFaceset($options) {
        $path = '/facepp/v3/faceset/getdetail';
        return $this->request($path, $options);
    }

    /**
     * delete faceset
     */
    public function deleteFaceset($options) {
        $path = '/facepp/v3/faceset/delete';
        return $this->request($path, $options);
    }

    /**
     * get faceset list
     */
    public function getFacesets($options) {
        $path = '/facepp/v3/faceset/getfacesets';
        return $this->request($path, $options);
    }

    /**
     * analyze face_token face
     */
    public function analyzeFace($options) {
        $path = '/facepp/v3/face/analyze';
        return $this->request($path, $options);
    }

    /**
     * get face token detail
     */
    public function getDetailFace($options) {
        $path = '/facepp/v3/face/getdetail';
        return $this->request($path, $options);
    }

    /**
     * set user id of the face token
     */
    public function setUserIdFace($options) {
        $path = '/facepp/v3/face/setuserid';
        return $this->request($path, $options);
    }

    /**
     * beauty user face
     */
    public function beautyFace($options) {
        $path = '/facepp/beta/beautify';
        return $this->request($path, $options);
    }

    /**
     * human body detect and analyze
     */
    public function detectHumanBody($options) {
        $path = '/humanbodypp/v1/detect';
        return $this->request($path, $options);
    }

    /**
     * segment human body
     */
    public function segmentHumanBody($options)
    {
        $path = '/humanbodypp/v2/segment';
        return $this->request($path, $options);
    }

    /**
     * analyze human body gesture
     */
    public function gestureHumanBody($options)
    {
        $path = '/humanbodypp/beta/gesture';
        return $this->request($path, $options);
    }

    /**
     * [OCR] detect id card
     */
    public function detectIdCard($options)
    {
        $path = '/cardpp/v1/ocridcard';
        return $this->request($path, $options);
    }

    /**
     * [OCR] detect driver license
     */
    public function detectDriverLicense($options)
    {
        $path = '/cardpp/v2/ocrdriverlicense';
        return $this->request($path, $options);
    }

    /**
     * [OCR] detect vehicle license
     */
    public function detectVehicleLicense($options)
    {
        $path = '/cardpp/v1/ocrvehiclelicense';
        return $this->request($path, $options);
    }

    /**
     * [OCR] detect bank card
     */
    public function detectBankCard($options)
    {
        $path = '/cardpp/v1/ocrbankcard';
        return $this->request($path, $options);
    }

    /**
     * detect scene object
     */
    public function detectSenceObject($options)
    {
        $path = '/imagepp/beta/detectsceneandobject';
        return $this->request($path, $options);
    }

    /**
     * recognize text
     */
    public function recognizeText($options)
    {
        $path = '/imagepp/v1/recognizetext';
        return $this->request($path, $options);
    }

    /**
     * merge face
     */
    public function mergeFace($options)
    {
        $path = '/imagepp/v1/mergeface';
        return $this->request($path, $options);
    }

    /**
     * detect license license plate
     */
    public function detectLicensePlate($options)
    {
        $path = '/imagepp/v1/licenseplate';
        return $this->request($path, $options);
    }

    /**
     * Singleton of adapter
     */
    public function getAdapter() {
        $adapter = $this->adapter;
        if (!isset($adapter)) {
            $adapter = new $this->adapterClass($this->headers, $this->connectTimeout, $this->socketTimeout);
            $adapter->setCurlOpts($this->curlOpts);
            $this->adapter = $adapter;
        }

        return $adapter;
    }

    /**
     * set adapter, if you need more control of the adapter, you can
     * construct a adapter, and then set it
     */
    public function setAdapter($adapter) {
        $this->adapter = $adapter;
    }

    public function getPublicForms()
    {
        return array(
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret
        );
    }

    /**
     * Send request
     * @return the response instance, see Response.php for more details
     */
    public function request($path, $options)
    {
        $forms = new MultiPartForm();
        // add public key field, for example api_key, api_secret
        $publics = $this->getPublicForms();
        $forms->addForms($publics);

        foreach ($options as $key => $value) {
            if (is_file($value)) {
                $forms->addFile($key, $value, file_get_contents($value));
            }else {
                $forms->addForm($key, $value);
            }
        }

        $headers = array('Content-Type' => $forms->getContentType());

        $adapter = $this->getAdapter();
        $url = $this->generateUrl($path);

        return $adapter->post($url, $forms, null, $headers);
    }

    /**
     * @param string $path the request uri, starts with '/''
     * @return string the request url
     */
    public function generateUrl($path) {
        return $this->host . '/' . trim($path, '/');
    }

    /**
     * Sets the http's timeout (in  millisecond)
     *
     * @param int $ms
     */
    public function setSocketTimeout($ms)
    {
        $this->socketTimeout = $ms;
    }

    /**
     * Sets the http's connection timeout (in millisecond)
     *
     * @param int $ms
     */
    public function setConnectTimeout($ms)
    {
        $this->connectTimeout = $ms;
    }

    /**
     * Set the public http's headers
     * @param array $headers
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    /**
     * set sslVerification
     */
    public function setSslVerification($ssl) {
        $this->sslVerification = $ssl;
    }

    /**
     * set curl options
     */
    public function setCurlOpts($conf)
    {
        $this->curlOpts = $conf;
    }
}

?>
