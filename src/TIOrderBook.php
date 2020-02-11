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
class TIOrderBook
{

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

    private $tradeStatus;
    private $minPriceIncrement;
    private $faceValue;
    private $lastPrice;
    private $closePrice;
    private $limitUp;
    private $limitDown;

    function __construct(
        $depth,
        $bids,
        $asks,
        $figi,
        $tradeStatus,
        $minPriceIncrement,
        $faceValue,
        $lastPrice,
        $closePrice,
        $limitUp,
        $limitDown
    ) {
        $this->depth = $depth;
        $this->asks = $asks;
        $this->bids = $bids;
        $this->figi = $figi;
        $this->tradeStatus = $tradeStatus;
        $this->minPriceIncrement = $minPriceIncrement;
        $this->faceValue = $faceValue;
        $this->lastPrice = $lastPrice;
        $this->closePrice = $closePrice;
        $this->limitUp = $limitUp;
        $this->limitDown = $limitDown;
    }

    function getDepth()
    {
        return $this->depth;
    }

    function getBestPricesToBuy()
    {
        return $this->asks;
    }

    function getBestPricesToSell()
    {
        return $this->bids;
    }

    function getFigi()
    {
        return $this->figi;
    }

    function getBestPriceToBuy()
    {
        return (count($this->asks) > 0) ? $this->asks[0][0] : null;
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

    /**
     * @return string
     */
    public function getTradeStatus()
    {
        return $this->tradeStatus;
    }

    /**
     * @return int
     */
    public function getMinPriceIncrement()
    {
        return $this->minPriceIncrement;
    }

    /**
     * @return int
     */
    public function getFaceValue()
    {
        return $this->faceValue;
    }

    /**
     * @return int
     */
    public function getLastPrice()
    {
        return $this->lastPrice;
    }

    /**
     * @return int
     */
    public function getClosePrice()
    {
        return $this->closePrice;
    }

    /**
     * @return int
     */
    public function getLimitUp()
    {
        return $this->limitUp;
    }

    /**
     * @return int
     */
    public function getLimitDown()
    {
        return $this->limitDown;
    }
}
