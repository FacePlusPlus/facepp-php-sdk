<?php
namespace Fpp;

require_once 'ContentTypes.php';
/**
 * A http request class
 */

class Request
{
    /**
     * The URL being requested.
     */
    public $url;

    /**
     * The URL query string
     */
    public $params;

    /**
     * The request raw body
     */
    public $body;

    /**
     * The headers being sent in the request.
     */
    public $headers;

    /**
     * The body being sent in the request.
     */
    public $data;

    /**
     * The method by which the request is being made.
     */
    public $method;

    /**
     * The Content Type
     */
    public $ctype;

    /**
     * Default useragent string to use.
     */
    public $useragent = 'FPP_PHP_SDK/1.0.0';

    /**
     * Construct a new instance of this class.
     *
     * @param string $url (Optional) The URL to request or service endpoint to query.
     * @param string $method (Optional) The method of the request
     * @param mixed $body (Optional) The request body
     * @param array $headers (Optional) The request headers
     * @param string $ctype (Optional) The Content-Type, see ContetType.php for detail
     * @return $this A reference to the current instance.
     */
    public function __construct($method, $url, $data, $params=array(), $headers=array())
    {
        // Set some default values.
        $this->url = $url;
        $this->params = $params;
        $this->method = strtoupper($method);
        $this->headers = $headers;
        $this->data = $data;
        $this->ctype = $headers['Content-Type'];
    }

    /**
     * Add a custom HTTP header to the cURL request.
     *
     * @param string $key (Required) The custom HTTP header to set.
     * @param mixed $value (Required) The value to assign to the custom HTTP header.
     * @return $this A reference to the current instance.
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Remove an HTTP header from the cURL request.
     *
     * @param string $key (Required) The custom HTTP header to set.
     * @return $this A reference to the current instance.
     */
    public function removeHeader($key)
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }
        return $this;
    }

    /**
     * Set the method type for the request.
     *
     * @param string $method (Required) One of the following constants: <HTTP_GET>, <HTTP_POST>, <HTTP_PUT>, <HTTP_HEAD>, <HTTP_DELETE>.
     * @return $this A reference to the current instance.
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Set a custom useragent string for the class.
     *
     * @param string $ua (Required) The useragent string to use.
     * @return $this A reference to the current instance.
     */
    public function setUserAgent($ua)
    {
        $this->useragent = $ua;
        return $this;
    }

    /**
     * Set the body to send in the request.
     *
     * @param string $data (Required) The textual content to send along in the body of the request.
     * @return $this A reference to the current instance.
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the request Content-Type
     */
    public function setContentType($ctype)
    {
        $this->ctype = $ctype;
    }

    /**
     * Set the URL to make the request to.
     *
     * @param string $url (Required) The URL to make the request to.
     * @return $this A reference to the current instance.
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     *
     * @param  string $url
     * @param  array $params
     * @return string
     */
    private function buildUrl(){
        if(!empty($this->params)){
            $str = http_build_query($this->params);
            $url = $this->url;
            $this->url = $url . (strpos($url, '?') === false ? '?' : '&') . $str;
        }
    }

    /**
     * Serialization the request data
     *
     * @param string $type the Content-Type, such as application/json
     */
    private function buildBody() {
        $body = '';
        if (is_array($this->data)) {
            switch ($this->ctype) {
                case ContentType::JSON:
                    $body = json_encode($this->data);
                    break;
                case ContentType::FORM:
                    $body = http_build_query($this->data);
                    break;
                default:
                    $body = (string)$this->data;
                    break;
            }
        }else {
            $body = (string)$this->data;
        }

        $this->body = $body;
    }

    /**
     * process the rquest headers, capitalize key words, add user-agent
     */
    private function buildHeader() {
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $key = ucwords($key);
            $headers[$key] = $value;
        }
        $headers['User-Agent'] = $this->useragent;
        if (!empty($this->ctype)){
            $headers['Content-Type'] = $this->ctype;
        }
        $this->headers = $headers;
    }

    /**
     * prepare request before send
     */
    public function prepare() {
        $this->buildUrl();
        $this->buildHeader();
        $this->buildBody();
    }

}

?>
