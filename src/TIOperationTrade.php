<?php


namespace jamesRUS52\TinkoffInvest;


class TIOperationTrade
{

    private $tradeId;
    private $date;
    private $price;
    private $quantity;

    public function __construct($tradeId, $date, $price, $quantity)
    {
        $this->tradeId = $tradeId;
        $this->date = $date;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getTradeId()
    {
        return $this->tradeId;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}