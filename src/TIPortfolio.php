<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIPortfolio
 *
 * @author james
 */
class TIPortfolio {
    //put your code here
    /**
     *
     * @var TIPortfolioCurrency 
     */
    private $currencies;
    /**
     *
     * @var TIPortfolioInstrument
     */
    private $instruments;
    
    function __construct($currencies, $instruments) {
        $this->currencies = $currencies;
        $this->instruments = $instruments;
    }
    
    /**
     * Get balance of currency
     * @param TICurrencyEnum $currency
     * @return double or false
     */
    public function getCurrencyBalance($currency)
    {
        foreach ($this->currencies as $curr)
        {
            if ($currency === $curr->getCurrency())
                return $curr->getBalance();
        }
        return false;
    }
    
    /**
     * Get Lots count of ticker
     * @param string $ticker
     * @return integer or false
     */
    public function getInstrumentLots($ticker)
    {
        foreach ($this->instruments as $instr)
        {
            if ($ticker === $instr->getTicker())
                return $instr->getLots();
        }
        return false;
    }
    
}
