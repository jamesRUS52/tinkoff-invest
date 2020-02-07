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
    const BUY_CARD = 'BuyCard';
    const BROKER_COMMISSION = 'BrokerCommission';
    const EXCHANGE_COMMISSION ='ExchangeCommission';
    const SERVICE_COMMISSION = 'ServiceCommission';
    const MARGIN_COMMISSION = 'MarginCommission';
    const OTHER_COMMISSION = 'OtherCommission';
    const PAY_IN = 'PayIn';
    const PAY_OUT = 'PayOut';
    const TAX = 'Tax';
    const TAX_LUCRE = 'TaxLucre';
    const TAX_DIVIDEND = 'TaxDividend';
    const TAX_COUPON = 'TaxCoupon';
    const TAX_BACK = 'TaxBack';
    const REPAYMENT = 'Repayment';
    const PART_REPAYMENT = 'PartRepayment';
    const COUPON = 'Coupon';
    const DIVIDEND = 'Dividend';
    const SECURITY_IN = 'SecurityIn';
    const SECURITY_OUT ='SecurityOut';

    /**
     * Get Operation enum
     *
     * @param string $operation
     *
     * @return string
     */
    public static function getOperation($operation)
    {
        switch ($operation)
        {
            case "Buy" : return TIOperationEnum::BUY;
                break;
            case "Sell" : return TIOperationEnum::SELL;
                break;
            case "BuyCard" : return TIOperationEnum::BUY_CARD;
                break;
            case "BrokerCommission" : return TIOperationEnum::BROKER_COMMISSION;
                break;
            case "ExchangeCommission" : return TIOperationEnum::EXCHANGE_COMMISSION;
                break;
            case "ServiceCommission" : return TIOperationEnum::SERVICE_COMMISSION;
                break;
            case "MarginCommission" : return TIOperationEnum::MARGIN_COMMISSION;
                break;
            case "OtherCommission" : return TIOperationEnum::OTHER_COMMISSION;
                break;
            case "PayIn" : return TIOperationEnum::PAY_IN;
                break;
            case "PayOut" : return TIOperationEnum::PAY_OUT;
                break;
            case "Tax" : return TIOperationEnum::TAX;
                break;
            case "TaxLucre" : return TIOperationEnum::TAX_LUCRE;
                break;
            case "TaxDividend" : return TIOperationEnum::TAX_DIVIDEND;
                break;
            case "TaxCoupon" : return TIOperationEnum::TAX_COUPON;
                break;
            case "TaxBack" : return TIOperationEnum::TAX_BACK;
                break;
            case "Repayment" : return TIOperationEnum::REPAYMENT;
                break;
            case "PartRepayment" : return TIOperationEnum::PART_REPAYMENT;
                break;
            case "Coupon" : return TIOperationEnum::COUPON;
                break;
            case "Dividend" : return TIOperationEnum::DIVIDEND;
                break;
            case "SecurityIn" : return TIOperationEnum::SECURITY_IN;
                break;
            case "SecurityOut" : return TIOperationEnum::SECURITY_OUT;
                break;
            default : return null;
        }
    }
}
