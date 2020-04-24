<?php


namespace jamesRUS52\TinkoffInvest;

use Exception;
use stdClass;

/**
 * Class TIResponse
 * @package jamesRUS52\TinkoffInvest
 */
class TIResponse
{
    /**
     * @var string
     */
    private $trackingId;
    /**
     * @var stdClass
     */
    private $payload;
    /**
     * @var string
     */
    private $status;

    /**
     * TIResponse constructor.
     * @param string $curlResponse
     * @param $curlStatusCode
     * @throws TIException
     */
    public function __construct($curlResponse, $curlStatusCode)
    {
        try {

            if (empty($curlResponse))
                throw new \Exception("Response is null");
            $result = json_decode($curlResponse);
            if (isset($result->trackingId) && isset($result->payload) && isset($result->status)) {
                $this->payload = $result->payload;
                $this->trackingId = $result->trackingId;
                $this->status = $result->status;
            } else {
                throw new TIException('Required fields are empty');
            }
            if ($this->status == 'Error') {
                throw new TIException($this->payload->message . ' [' . $this->payload->code . ']');
            }
        }
        catch (TIException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            switch ($curlStatusCode) {
                case 401:
                    $error_message = "Authorization error";
                    break;
                case 429:
                    $error_message = "Too Many Requests";
                    break;
                default:
                    $error_message = "Unknown error";
                    break;
            }
            throw new TIException($error_message);
        }
    }


    /**
     * @return stdClass
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

}