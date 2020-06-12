<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIOrder
 *
 * @author james
 */
class TIOrder {
    //put your code here
    
    private $orderId;
    private $operation;
    private $status;
    private $rejectReason;
    private $requestedLots;
    private $executedLots;
    private $commission;
    private $figi;
    private $type;
    private $message;
    private $price;
            
    function __construct($orderId, $operation, $status, $rejectReason, $requestedLots, $executedLots, $commission, $figi, $type, $message, $price) {
        $this->orderId = $orderId;
        $this->operation = $operation;
        $this->status = $status;
        $this->rejectReason = $rejectReason;
        $this->requestedLots = $requestedLots;
        $this->executedLots = $executedLots;
        $this->commission = $commission;
        $this->figi = $figi;
        $this->type = $type;
        $this->message = $message;
        $this->price = $price;
    }

    function getOrderId() {
        return $this->orderId;
    }

    function getOperation() {
        return $this->operation;
    }

    function getStatus() {
        return $this->status;
    }

    function getRejectReason() {
        return $this->rejectReason;
    }

    function getRequestedLots() {
        return $this->requestedLots;
    }

    function getExecutedLots() {
        return $this->executedLots;
    }

    /**
     * @return TICommission
     */
    function getCommission() {
        return $this->commission;
    }

    function getFigi() {
        return $this->figi;
    }
    
    function getType() {
        return $this->type;
    }

    function getMessage(){
        return $this->message;
    }

    function getPrice() {
        return $this->price;
    }
}
