<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest\Tests;

use \PHPUnit\Framework\TestCase;
use jamesRUS52\TinkoffInvest\TIClient;
use jamesRUS52\TinkoffInvest\TISiteEnum;
use jamesRUS52\TinkoffInvest\TICurrencyEnum;
use jamesRUS52\TinkoffInvest\TIInstrument;
use jamesRUS52\TinkoffInvest\TIPortfolio;
use jamesRUS52\TinkoffInvest\TIOrder;
use jamesRUS52\TinkoffInvest\TIOperationEnum;
/**
 * Description of TIClientTest
 *
 * @author james
 */
class TIClientTest extends TestCase {
    //put your code here
    /**
     *
     * @var TIClient
     * 
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new TIClient("t.YhKXlD1uriHNqafx5xw9i_fBoP_NVwh2r2l0h5CVPgVqpvO0WVCn5OvbcRnPg2kQH7kO67rh8dHZju32QsFxTw",TISiteEnum::SANDBOX);
    }

    protected function tearDown()
    {
        $this->fixture = null;
    }

    
    public function testsbRegister()
    {
        $status = $this->fixture->sbRegister();
        $this->assertEquals("Ok", $status);
    }
    
    public function testsbClear()
    {
        $status = $this->fixture->sbClear();
        $this->assertEquals("Ok", $status);
    }
    
    public function testsbPositionBalance()
    {
        $status = $this->fixture->sbPositionBalance(100, "BBG0013HGFT4");
        $this->assertEquals("Ok", $status);
    }
    
    public function testsbCurrencyBalance()
    {
        $status = $this->fixture->sbCurrencyBalance(5000000, TICurrencyEnum::RUB);
        $this->assertEquals("Ok", $status);
    }
    
    public function testgetStocks()
    {
        $stocks = $this->fixture->getStocks();
        $this->assertGreaterThan(1, count($stocks));
        $this->assertInstanceOf(TIInstrument::class, $stocks[0]);
        
        $stock = $this->fixture->getStocks(["SBER"]);
        $this->assertCount(1, $stock);
    }
    
    public function testgetBonds()
    {
        $bonds = $this->fixture->getBonds();
        $this->assertGreaterThan(1, count($bonds));
        $this->assertInstanceOf(TIInstrument::class, $bonds[0]);
        
        $bond = $this->fixture->getBonds(["SU26227RMFS7"]);
        $this->assertCount(1, $bond);
    }
    
    public function testgetEtfs()
    {
        $etfs = $this->fixture->getEtfs();
        $this->assertGreaterThan(1, count($etfs));
        $this->assertInstanceOf(TIInstrument::class, $etfs[0]);
        
        $etf = $this->fixture->getEtfs(["FXTB"]);
        $this->assertCount(1, $etf);
    }
    
    public function testgetCurrencies()
    {
        $etfs = $this->fixture->getCurrencies();
        $this->assertGreaterThan(1, count($etfs));
        $this->assertInstanceOf(TIInstrument::class, $etfs[0]);
        
        $etf = $this->fixture->getCurrencies(["USD000UTSTOM"]);
        $this->assertCount(1, $etf);
    }
    
    public function testgetInstrumentByTicker()
    {
        $instrument = $this->fixture->getInstrumentByTicker("SBER");
        $this->assertInstanceOf(TIInstrument::class, $instrument);
    }
    
    public function testgetInstrumentByFigi()
    {
        $instrument = $this->fixture->getInstrumentByFigi("BBG0013HGFT4");
        $this->assertInstanceOf(TIInstrument::class, $instrument);
    }
    
    public function testgetPortfolio()
    {
        $portfolio = $this->fixture->getPortfolio();
        $this->assertInstanceOf(TIPortfolio::class, $portfolio);
    }
    
    public function testsendOrder()
    {
        $order = $this->fixture->sendOrder("BBG0013HGFT4", 7, TIOperationEnum::BUY, 100);
        $this->assertInstanceOf(TIOrder::class, $order);
        $this->assertEquals("Fill", $order->getStatus());
        
        $portfolio = $this->fixture->getPortfolio();
        $lots = $portfolio->getInstrumentLots($this->fixture->getInstrumentByFigi("BBG0013HGFT4")->getTicker());
        $this->assertEquals(7, $lots);
        
        /* we can't check this in sandbox
        $orders = $this->fixture->getOrders([$order->getOrderId()]);
        $this->assertCount(1, $orders);
        
        $statusCancel = $this->fixture->cancelOrder($order->getOrderId());
        $this->assertEquals("Ok", $statusCancel);
        
        $orders = $this->fixture->getOrders([$order->getOrderId()]);
        $this->assertCount(0, $orders);
         */
    }
    
    public function testgetOperations()
    {
        $from = new \DateTime();
        $to = new \DateTime();
        $from->sub(new \DateInterval("P1D"));
        $operations = $this->fixture->getOperations($from, $to);
        $this->assertGreaterThan(1, $operations);

    }
}
