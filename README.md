# PHP client for Tinkoff invest API (PHP клиент для API Тинькофф инвестиции)

![](https://github.com/jamesRUS52/tinkoff-invest/workflows/Tests/badge.svg)

## How to install
```
composer require james.rus52/tinkoffinvest
```
or
add to your compose.json
```json
{
    "require": {
        "james.rus52/tinkoffinvest": "^0.*"
    }
}
```
and then
```
composer install
```
## How to use
Include classes via autoloader
```php
require_once 'vendor/autoload.php';

use \jamesRUS52\TinkoffInvest\TIClient;
use \jamesRUS52\TinkoffInvest\TISiteEnum;
use \jamesRUS52\TinkoffInvest\TICurrencyEnum;
use \jamesRUS52\TinkoffInvest\TIInstrument;
use \jamesRUS52\TinkoffInvest\TIPortfolio;
use \jamesRUS52\TinkoffInvest\TIOperationEnum;
use \jamesRUS52\TinkoffInvest\TIIntervalEnum;
use \jamesRUS52\TinkoffInvest\TICandleIntervalEnum;
use \jamesRUS52\TinkoffInvest\TICandle;
use \jamesRUS52\TinkoffInvest\TIOrderBook;
use \jamesRUS52\TinkoffInvest\TIInstrumentInfo;

```
create token to use tinkoff invest on [Tinkoff invest setting page](https://www.tinkoff.ru/invest/settings/)

Create client instance for sandbox 
```php
$client = new TIClient("TOKEN",TISiteEnum::SANDBOX);
```
or real exchange
```php
$client = new TIClient("TOKEN",TISiteEnum::EXCHANGE);
```
Put money to your sandbox account (sandbox only)
```php
$client->sbCurrencyBalance(500,TICurrencyEnum::USD);
```
Client register on sandbox (sandbox only)
```php
$client->sbRegister();
```
Put stocks to your sandbox account (sandbox only)
```php
$client->sbPositionBalance(10.4,"BBG000BR37X2");
```
Clear all positions on sandbox (sandbox only)
```php
$client->sbClear();
```
Get all stocks/bonds/etfs/currencies from market
```php
$stockes = $client->getStocks();
$instr = $client->getBonds();
$instr = $client->getEtfs();
$instr = $client->getCurrencies();
```
or with filter
```php
$stockes = $client->getStocks(["V","LKOH"]);
$instr = $client->getBonds(["RU000A0JX3X7"]);
$instr = $client->getEtfs(["FXRU"]);
$instr = $client->getCurrencies(["USD000UTSTOM"]);
```
Get instrument by ticker
```php
$instr = $client->getInstrumentByTicker("AMZN");
```
or by figi
```php
$instr = $client->getInstrumentByFigi("BBG000BR37X2");
```

Get accounts
```php
$accounts = $client->getAccounts(); 
```

Get portfolio (if null, used default Tinkoff account) 
```php
$port = $client->getPortfolio(TIAccount $account = null);
```
Get portfolio balance
```php
print $port->getCurrencyBalance(TICurrencyEnum::RUB);
```
Get instrument lots count
```php
print $port->getinstrumentLots("PGR");
```
Send order
```php
$order = $client->sendOrder("BBG000BVPV84", 1, TIOperationEnum::BUY, 1.2);
print $order->getOrderId();
```
Cancel order
```php
$client->cancelOrder($order->getOrderId());
```
List of operations from 10 days ago to 30 days period
```php
$dateFrom = new \DateTime();
$dateFrom->sub(new \DateInterval("P10D"));
$operations = $client->getOperations(new \DateTime(), TIIntervalEnum::DAY30);
foreach ($operations as $operation)
  print $operation->getId ().' '.$operation->getFigi (). ' '.$operation->getPrice ().' '.$operation->getOperationType().' '.$operation->getDate()->format('d.m.Y H:i')."\n";

```
Getting instrument status
```php
$status = $client->getInstrumentInfo($sber->getFigi());
print 'Instrument status: '. $status->getTrade_status()."\n";
```

Get Candles and Order books
```php
if ($status->getTrade_status()=="normal_trading")
{
        $candle = $client->getCandle($sber->getFigi(), TICandleIntervalEnum::DAY);
        print 'Low: '.$candle->getLow(). ' High: '.$candle->getHigh().' Open: '.$candle->getOpen().' Close: '.$candle->getClose().' Volume: '.$candle->getVolume()."\n";

        $orderbook = $client->getOrderBook($sber->getFigi(),2);
        print 'Price to buy: '.$orderbook->getBestPriceToBuy().' Available lots: '.$orderbook->getBestPriceToBuyLotCount().' Price to Sell: '.$orderbook->getBestPriceToSell().' Available lots: '.$orderbook->getBestPriceToSellLotCount()."\n";
}
```

You can also to subscribe on changes order books, candles or instrument info:
First of all, make a callback function to manage events:
```php
function action($obj)
{
        print "action\n";
        if ($obj instanceof TICandle)
            print 'Time: '.$obj->getTime ()->format('d.m.Y H:i:s').' Volume: '.$obj->getVolume ()."\n";
        if ($obj instanceof TIOrderBook)
            print 'Price to Buy: '.$obj->getBestPriceToBuy().' Price to Sell: '.$obj->getBestPriceToSell()."\n";
}
```
Then subscribe to events
```php
$client->subscribeGettingCandle($sber->getFigi(), TICandleIntervalEnum::MIN1);
$client->subscribeGettingOrderBook($sber->getFigi(), 2);
```
and finaly start listening new events 
```php
$client->startGetting("action",20,60);
```
in this example we awaiting max 20 respnse and max for 60 seconds
if you want no limits, you should make
```php
$client->startGetting("action");
$client->startGetting("action",null,600);
$client->startGetting("action",1000,null);
```
to stop listening do
```php
$client->stopGetting();
```

###CAUTION
If you use subscriptions you should check figi on response, because you getting all subscribed instruments in one queue

## Donation
Please support my project

[![](https://img.shields.io/badge/Donate-PayPal-green)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4WEWSZPBUBSVJ&source=url)
[![](https://img.shields.io/badge/Donate-Yandex-green)](https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=Project%20support&targets-hint=&default-sum=100&button-text=14&payment-type-choice=on&mobile-payment-type-choice=on&hint=&successURL=&quickpay=shop&account=41001102505770)
[![](https://img.shields.io/badge/Donate-WebMoney-green)](https://funding.webmoney.ru/widgets/horizontal/f892576d-1ce5-4046-abd7-7c947a81b398?hs=1&bt=0&sum=100)

## Licence 
MIT
