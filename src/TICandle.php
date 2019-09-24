<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TICandle
 *
 * @author james
 */
class TICandle {
    //put your code here
    private $open;
    private $close;
    private $high;
    private $low;
    private $volume;
    /**
     *
     * @var \DateTime
     */
    private $time;
    /**
     *
     * @var TICandleIntervalEnum
     */
    private $interval;
    private $figi;
    
    function __construct($open, $close, $high, $low, $volume, \DateTime $time, $interval, $figi) {
        $this->open = $open;
        $this->close = $close;
        $this->high = $high;
        $this->low = $low;
        $this->volume = $volume;
        $this->time = $time;
        $this->interval = $interval;
        $this->figi = $figi;
    }
    
    function getOpen() {
        return $this->open;
    }

    function getClose() {
        return $this->close;
    }

    function getHigh() {
        return $this->high;
    }

    function getLow() {
        return $this->low;
    }

    function getVolume() {
        return $this->volume;
    }

    function getTime() {
        return $this->time;
    }

    function getInterval() {
        return $this->interval;
    }

    function getFigi() {
        return $this->figi;
    }



}
