<?php
/**
 * Curl Request Object
 *
 * @author Aaron Saray
 */

namespace iMoneza\Request;

/**
 * Class CurlRequest
 * @package iMoneza
 */
class Curl implements RequestInterface
{
    /**
     * @var Resource
     */
    protected $handle;

    /**
     * Curl constructor.
     * Set default values
     */
    public function __construct()
    {
        $this->handle = curl_init();
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Sets the request for this URL
     *
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->setOption(CURLOPT_URL, $url);
        return $this;
    }

    /**
     * Execute the request
     * @return bool|string
     */
    public function execute()
    {
        return curl_exec($this->handle);
    }

    /**
     * Get all the response info for this request
     *
     * @return mixed
     */
    public function getResponseInfo()
    {
        return curl_getinfo($this->handle);
    }

    /**
     * @return integer the response code
     */
    public function getResponseHTTPCode()
    {
        return curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
    }

    /**
     * @return string the potential error string
     */
    public function getErrorString()
    {
        return curl_error($this->handle);
    }

    /**
     * @return int the error code
     */
    public function getErrorCode()
    {
        return curl_errno($this->handle);
    }

    /**
     * Set curl option
     *
     * @param $name
     * @param $value
     * @return $this
     */
    protected function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
        return $this;
    }
}