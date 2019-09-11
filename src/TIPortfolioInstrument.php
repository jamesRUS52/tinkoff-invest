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
    
    function __construct($figi, $ticker, $isin, $instrumentType, $balance, $lots, $expectedYieldValue,$expectedYieldCurrency) {
        $this->figi = $figi;
        $this->ticker = $ticker;
        $this->isin = $isin;
        $this->instrumentType = $instrumentType;
        $this->balance = $balance;
        $this->lots = $lots;
        $this->expectedYieldValue = $expectedYieldValue;
        $this->expectedYieldCurrency = $expectedYieldCurrency;
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

    function getLots() {
        return $this->lots;
    }

    function getExpectedYieldValue() {
        return $this->expectedYieldValue;
    }
    
    function getExpectedYieldCurrency() {
        return $this->expectedYieldCurrency;
    }


            
}
