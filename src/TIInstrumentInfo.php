<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIInstrumentInfo
 *
 * @author james
 */
class TIInstrumentInfo {
    //put your code here
    private $trade_status;
    private $min_price_increment;
    private $lot;
    private $accrued_interest;
    private $limit_up;
    private $limit_down;
    private $figi;
    
    function __construct($trade_status, $min_price_increment, $lot, $figi) {
        $this->trade_status = $trade_status;
        $this->min_price_increment = $min_price_increment;
        $this->lot = $lot;
        $this->figi = $figi;
    }

    function getTrade_status() {
        return $this->trade_status;
    }

    function getMin_price_increment() {
        return $this->min_price_increment;
    }

    function getLot() {
        return $this->lot;
    }

    function getAccrued_interest() {
        return $this->accrued_interest;
    }

    function getLimit_up() {
        return $this->limit_up;
    }

    function getLimit_down() {
        return $this->limit_down;
    }

    function getFigi() {
        return $this->figi;
    }

    function setAccrued_interest($accrued_interest) {
        $this->accrued_interest = $accrued_interest;
    }

    function setLimit_up($limit_up) {
        $this->limit_up = $limit_up;
    }

    function setLimit_down($limit_down) {
        $this->limit_down = $limit_down;
    }


    
}
