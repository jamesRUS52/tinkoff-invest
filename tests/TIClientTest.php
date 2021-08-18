<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest\Tests;

use jamesRUS52\TinkoffInvest\TIAccount;
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

    protected function setUp(): void
    {
        $this->fixture = new TIClient("t.RJiVzRcOKf5h0eUJBDsy9aggjgX3FGU82O-4j_Cu2qpM-_yPYwjJMsBDQWObCagCKFNwCvFl-iDlFtBK9KwK_w",TISiteEnum::SANDBOX);
        $this->fixture->setIgnoreSslPeerVerification(true);
    }

    protected function tearDown(): void
    {
        $this->fixture->sbClear();
        $this->fixture = null;
    }

    
    public function testsbRegister()
    {
        $account = $this->fixture->sbRegister();
        $this->assertInstanceOf(TIAccount::class, $account);
    }
    
    public function testsbClear()
    {
        $status = $this->fixture->sbClear();
        $this->assertEquals("Ok", $status);
    }
    
    public function testsbPositionBalance()
    {
        $status = $this->fixture->sbPositionBalance(100, "BBG004730N88");
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
        $this->assertContainsOnlyInstancesOf(TIInstrument::class,$etfs);

        $etf = $this->fixture->getCurrencies(["EUR_RUB__TOM"]);
        $this->assertCount(1, $etf);
    }
    
    public function testgetInstrumentByTicker()
    {
        $instrument = $this->fixture->getInstrumentByTicker("SBER");
        $this->assertInstanceOf(TIInstrument::class, $instrument);
    }
    
    public function testgetInstrumentByFigi()
    {
        $instrument = $this->fixture->getInstrumentByFigi("BBG004730N88");
        $this->assertInstanceOf(TIInstrument::class, $instrument);
    }
    
    public function testgetPortfolio()
    {
        $portfolio = $this->fixture->getPortfolio();
        $this->assertInstanceOf(TIPortfolio::class, $portfolio);
    }
    
    public function testsendOrder()
    {
        $this->fixture->sbCurrencyBalance(5000000, TICurrencyEnum::RUB);
        
        $order = $this->fixture->sendOrder("BBG004RVFCY3", 11, TIOperationEnum::BUY, 100);
        $this->assertInstanceOf(TIOrder::class, $order);
        $this->assertEquals("Fill", $order->getStatus());
        
        $portfolio = $this->fixture->getPortfolio();
        $lots = $portfolio->getInstrumentLots($this->fixture->getInstrumentByFigi("BBG004RVFCY3")->getTicker());
        $this->assertEquals(11, $lots);
        
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
        $from = new \DateTime('-3 day');
        $to = new \DateTime();
        $operations = $this->fixture->getOperations($from, $to,'BBG004RVFCY3');
        $this->assertGreaterThan(1, $operations);

    }

    public function testgetBestPriceToBuy()
    {
        $ordBook = $this->fixture->getHistoryOrderBook("BBG004RVFCY3");
        $this->assertIsNumeric($ordBook->getBestPriceToBuy());
        $this->assertIsInt($ordBook->getBestPriceToBuyLotCount());
    }

    public function testgetAccounts()
    {
        $accounts = $this->fixture->getAccounts();
        $this->assertGreaterThan(1,$accounts);
    }
}
