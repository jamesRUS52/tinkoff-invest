# PHP client for Tinkoff invest API (PHP клиент для API Тинькофф инвестиции)

## How to install
```
composer require james.rus52/tinkoffinvest
```
or
add to your compose.json
```json
{
    "require": {
        "james.rus52/tinkoffinvest": "^0.1.0"
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
Get prtfolio
```php
$port = $client->getPortfolio();
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
$order = $client->sendOrder("BBG000BVPV84", 1, \jamesRUS52\TinkoffInvest\TIOperationEnum::BUY, 1.2);
print $order->getORderId();
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
## Donation
Please support my project

[![](https://img.shields.io/badge/Donate-PayPal-green)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4WEWSZPBUBSVJ&source=url)
[![](https://img.shields.io/badge/Donate-Yandex-green)](https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=Project%20support&targets-hint=&default-sum=100&button-text=14&payment-type-choice=on&mobile-payment-type-choice=on&hint=&successURL=&quickpay=shop&account=41001102505770)
[![](https://img.shields.io/badge/Donate-WebMoney-green)](https://funding.webmoney.ru/widgets/horizontal/f892576d-1ce5-4046-abd7-7c947a81b398?hs=1&bt=0&sum=100)

## Licence 
MIT
