<?php


namespace jamesRUS52\TinkoffInvest;


class TICommission
{

    private $currency;

    private $value;

    public function __construct($currency, $value)
    {
        $this->currency = $currency;
        $this->value    = $value;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getValue()
    {
        return $this->getValue();
    }

}