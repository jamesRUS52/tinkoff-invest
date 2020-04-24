<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIPortfolioCurrency
 *
 * @author james
 */
class TIPortfolioCurrency {
    //put your code here
    private $balance;
    private $currency;
    private $blocked;
                
    function __construct($balance, $currency, $blocked) {
        $this->balance = $balance;
        $this->currency = $currency;
        $this->blocked = $blocked;
    }
    
    function getBalance() {
        return $this->balance;
    }

    function getCurrency() {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getBlocked()
    {
        return $this->blocked;
    }



}
