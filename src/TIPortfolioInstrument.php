<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIPortfolioInstrument
 *
 * @author james
 */
class TIPortfolioInstrument {
    //put your code here
    private $figi;
    private $ticker;
    private $isin;
    private $instrumentType;
    private $balance;
    private $lots;
    private $expectedYieldValue;
    private $expectedYieldCurrency;
    private $blocked;
    private $name;
    private $averagePositionPrice;
    private $averagePositionPriceNoNkd;
    
    function __construct($figi, $ticker, $isin, $instrumentType, $balance, $blocked, $lots, $expectedYieldValue,$expectedYieldCurrency, $name, $averagePositionPrice, $averagePositionPriceNoNkd) {
        $this->figi = $figi;
        $this->ticker = $ticker;
        $this->isin = $isin;
        $this->instrumentType = $instrumentType;
        $this->balance = $balance;
        $this->blocked = $blocked;
        $this->lots = $lots;
        $this->expectedYieldValue = $expectedYieldValue;
        $this->expectedYieldCurrency = $expectedYieldCurrency;
        $this->name = $name;
        $this->averagePositionPrice = $averagePositionPrice;
        $this->averagePositionPriceNoNkd = $averagePositionPriceNoNkd;
    }

    function getFigi() {
        return $this->figi;
    }

    function getTicker() {
        return $this->ticker;
    }

    function getIsin() {
        return $this->isin;
    }

    function getInstrumentType() {
        return $this->instrumentType;
    }

    function getBalance() {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    function getLots() {
        return $this->lots;
    }

    function getExpectedYieldValue() {
        return $this->expectedYieldValue;
    }
    
    function getExpectedYieldCurrency() {
        return $this->expectedYieldCurrency;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAveragePositionPrice()
    {
        return $this->averagePositionPrice;
    }

    /**
     * @return mixed
     */
    public function getAveragePositionPriceNoNkd()
    {
        return $this->averagePositionPriceNoNkd;
    }


            
}
