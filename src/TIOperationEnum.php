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
abstract class TIOperationEnum {
    //put your code here
    const BUY = "Buy";
    const SELL = "Sell";
    
    /**
     * Get Operation enum
     * @param string $operation
     * @return TIOperationEnum
     */
    public static function getOperation($operation)
    {
        switch ($operation)
        {
            case "Buy" : return TIOperationEnum::BUY;
                break;
            case "Sell" : return TIOperationEnum::SELL;
                break;
            default : return null;
        }
    }
}
