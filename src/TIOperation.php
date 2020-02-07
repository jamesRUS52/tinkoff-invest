<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIOperation
 *
 * @author james
 */
class TIOperation {
    //put your code here
    
    private $id;
    private $status;
    private $trades;
    private $commission;
    private $currency;
    private $payment;
    private $price;
    private $quantity;
    private $figi;
    private $instrumentType;
    private $isMarginCall;
    private $date;
    private $operationType;
    
    function __construct($id, $status, $trades, $commission , $currency, $payment, $price, $quantity, $figi, $instrumentType, $isMarginCall, $date, $operationType) {
        $this->id = $id;
        $this->status = $status;
        $this->trades = $trades;
        $this->commission = $commission;
        $this->currency = $currency;
        $this->payment = $payment;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->figi = $figi;
        $this->instrumentType = $instrumentType;
        $this->isMarginCall = $isMarginCall;
        $this->date = $date;
        $this->operationType = $operationType;
    }

    function getId() {
        return $this->id;
    }

    function getStatus() {
        return $this->status;
    }

    function getTrades() {
        return $this->trades;
    }

    function getCommission() {
        return $this->commission;
    }

    function getCurrency() {
        return $this->currency;
    }

    function getPayment() {
        return $this->payment;
    }

    function getPrice() {
        return $this->price;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getFigi() {
        return $this->figi;
    }

    function getInstrumentType() {
        return $this->instrumentType;
    }

    function getIsMarginCall() {
        return $this->isMarginCall;
    }

    function getDate() {
        return $this->date;
    }

    function getOperationType() {
        return $this->operationType;
    }


    
}
