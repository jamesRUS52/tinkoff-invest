<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIOrderBook
 *
 * @author james
 */
class TIOrderBook {
    //put your code here
    /**
     * depth of orderbook
     * @var integer
     */
    private $depth;
    /**
     * price|count array of bids
     * @var array 
     */
    private $asks = array();
    /**
     * price|count array of asks
     * @var array
     */
    private $bids = array();
    /**
     * FIGI
     * @var string
     */
    private $figi;
    
    function __construct($depth, $bids, $asks, $figi) {
        $this->depth = $depth;
        $this->asks = $asks;
        $this->bids = $bids;
        $this->figi = $figi;
    }

    function getDepth() {
        return $this->depth;
    }

    function getBestPricesToBuy() {
        return $this->asks;
    }

    function getBestPricesToSell() {
        return $this->bids;
    }

    function getFigi() {
        return $this->figi;
    }
    
    function getBestPriceToBuy()
    {
        return (count($this->asks)>0) ? $this->asks[0][0] : null;
    }
    
    function getBestPriceToBuyLotCount()
    {
        return $this->asks[0][1];
    }
    
    function getBestPriceToSell()
    {
        return $this->bids[0][0];
    }
    
    function getBestPriceToSellLotCount()
    {
        return $this->asks[1][1];
    }


}
