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
     * @var array
     */
    private $payload;
    /**
     * @var string
     */
    private $status;

    /**
     * TIResponse constructor.
     * @param string $curlResponse
     * @throws TIException
     */
    public function __construct($curlResponse)
    {
        try {
            $result = json_decode($curlResponse);
            if (!empty($result['trackingId']) && !empty($result['payload']) && !empty($result['status'])) {
                $this->payload = $result['payload'];
                $this->trackingId = $result['trackingId'];
                $this->status = $result['status'];
            } else {
                throw new TIException('Required fields are empty');
            }
            if ($this->status == 'Error') {
                throw new TIException('errorMessage = ' . $this->payload->message . ' errorCode = ' . $this->payload->code);
            }
        } catch (Exception $e) {
            throw new TIException('Can not decode response from api');
        }
    }


    /**
     * @return stdClass
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function getStatus()
    {
        return $this->status;
    }

}