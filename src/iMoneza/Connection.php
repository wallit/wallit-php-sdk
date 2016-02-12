<?php
/**
 * The connection to iMoneza
 *
 * @author Aaron Saray
 */

namespace iMoneza;
use iMoneza\Exception;
use iMoneza\Request\RequestInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class Connection
 * @package iMoneza
 */
class Connection
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var string the base of the access api
     */
    protected $baseURLAccessAPI = 'https://accessapi.imoneza.com';

    /**
     * Connection constructor.
     * @param $apiKey string
     * @param $secretKey string
     * @param RequestInterface $request
     * @param LoggerInterface $log
     */
    public function __construct($apiKey, $secretKey, RequestInterface $request, LoggerInterface $log)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->request = $request;
        $this->log = $log;
    }

    /**
     * @return mixed
     * @throws Exception\AccessDenied
     * @throws Exception\TransferError
     */
    public function request()
    {
        $url = $this->baseURLAccessAPI;

        $this->debug('Set URL', [$url]);
        $this->request->setUrl($url);

        $this->debug('Beginning request');
        $result = $this->request->execute();
        $this->debug('Request completed.', ['INFO' => $this->request->getResponseInfo(), 'BODY' => $result]);

        $this->handleRequestError($result);

        // really shouldn't happen unless something changes or I missed something
        if (($httpCode = $this->request->getResponseHTTPCode()) !== 200) {
            $message = "HTTP Error Code of {$httpCode} was generated and not caught: " . $result;
            $this->log->error($message);
            throw new Exception\TransferError($message);
        }

        $this->debug('All error checking passed - returning result.');

        return $result;
    }

    /**
     * @param $url string the base URL in case it is different (useful for testing)
     */
    public function setBaseURLAccessAPI($url)
    {
        $this->baseURLAccessAPI = $url;
    }

    /**
     * Handles a potential request error by throwing a useful exception
     * @param $result string|false
     * @throws Exception\AccessDenied
     * @throws Exception\TransferError
     */
    protected function handleRequestError($result)
    {
        /**
         * Curl error
         */
        if ($result === false) {
            throw new Exception\TransferError($this->request->getErrorString(), $this->request->getErrorCode());
        }

        switch ($this->request->getResponseHTTPCode()) {
            case 403:
                $this->log->error($result, ['CODE'=>403]);
                throw new Exception\AccessDenied($result, 403);
                break;
        }
    }

    /**
     * @param $string
     * @param array $context
     * @return bool|null
     */
    protected function debug($string, $context = [])
    {
        return $this->log->debug($string, $context);
    }
}