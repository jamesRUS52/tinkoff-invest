<?php


namespace jamesRUS52\TinkoffInvest;


class TIAccount
{

    private $brokerAccountType;

    private $brokerAccountId;

    public function __construct($type, $id)
    {
        $this->brokerAccountType = $type;
        $this->brokerAccountId   = $id;
    }

    function getBrokerAccountType(){
        return $this->brokerAccountType;
    }

    function getBrokerAccountId(){
        return $this->brokerAccountId;
    }

}