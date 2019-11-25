<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIInstrument
 *
 * @author james
 */
class TIInstrument {
    //put your code here
    private $figi;
    private $ticker;
    private $isin;
    private $minPriceIncrement;
    private $lot;
    private $currency;
    private $name;

    function __construct($figi, $ticker, $isin, $minPriceIncrement, $lot, $currency, $name) {
        $this->figi = $figi;
        $this->currency = $currency;
        $this->ticker = $ticker;
        $this->isin = $isin;
        $this->minPriceIncrement = $minPriceIncrement;
        $this->lot = $lot;
        $this->name = $name;
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

    function getMinPriceIncrement() {
        return $this->minPriceIncrement;
    }

    function getLot() {
        return $this->lot;
    }

    function getCurrency() {
        return $this->currency;
    }

    function getName() {
        return $this->name;
    }


}
