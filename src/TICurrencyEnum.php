<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TISite
 *
 * @author james
 */
abstract class TICurrencyEnum {
    //put your code here
    const RUB = "RUB";
    const USD = "USD";
    const EUR = "EUR";
    
    /**
     * 
     * @param string $currency
     * @return TICurrencyEnum
     */
    public static function getCurrency($currency)
    {
        switch ($currency)
        {
            case "RUB" : return TICurrencyEnum::RUB;
                break;
            case "USD" : return TICurrencyEnum::USD;
                break;
            case "EUR" : return TICurrencyEnum::EUR;
                break;
            default : return null;
        }
    }
}
