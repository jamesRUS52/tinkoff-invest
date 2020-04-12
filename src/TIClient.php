<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

use WebSocket\BadOpcodeException;
use WebSocket\Client;
use Exception;
use DateTime;
use DateInterval;

/**
 * Description of TIClient
 *
 * @author james
 */
class TIClient
{

    //put your code here
    /**
     * @var string
     */
    private $token;

    /**
     * @var TISiteEnum
     */
    private $url;

    /**
     *
     * @var Client
     */
    private $wsClient;

    /**
     * @var bool
     */
    private $startGetting = false;

    /**
     * @var int
     */
    private $response_now = 0;

    /**
     * @var
     */
    private $response_start_time;

    /**
     *
     * @param string $token token from tinkoff.ru for specific site
     * @param TISiteEnum $site site name (sandbox or real exchange)
     * @throws TIException
     */
    function __construct($token, $site)
    {
        $this->token = $token;
        $this->url = $site;
        $this->wsConnect();
    }


    /**
     * Удаление всех позиций в песочнице
     *
     * @return string status
     * @throws TIException
     */
    public function sbClear()
    {
        $response = $this->sendRequest("/sandbox/clear", "POST");
        return $response->getStatus();
    }

    /**
     * Регистрация клиента в sandbox
     *
     * @return string status
     * @throws TIException
     */
    public function sbRegister()
    {
        $response = $this->sendRequest("/sandbox/register", "POST");
        return $response->getStatus();
    }

    /**
     * Выставление баланса по инструментным позициям
     *
     * @param double $balance
     * @param string $figi
     *
     * @return string status
     * @throws TIException
     */
    public function sbPositionBalance($balance, $figi)
    {
        $request = ["figi" => $figi, "balance" => $balance];
        $request_body = json_encode($request, JSON_NUMERIC_CHECK);
        $response = $this->sendRequest(
            "/sandbox/positions/balance",
            "POST",
            [],
            $request_body
        );
        return $response->getStatus();
    }

    /**
     * Выставление баланса по инструментным позициям
     *
     * @param double $balance
     * @param string $currency
     *
     * @return string status
     * @throws TIException
     */
    public function sbCurrencyBalance($balance, $currency = TICurrencyEnum::RUB)
    {
        $request = ["currency" => $currency, "balance" => $balance];
        $request_body = json_encode($request, JSON_NUMERIC_CHECK);
        $response = $this->sendRequest(
            "/sandbox/currencies/balance",
            "POST",
            [],
            $request_body
        );
        return $response->getStatus();
    }

    /**
     * Получение списка акций
     *
     * @param array $tickers Ticker Filter
     *
     * @return TIInstrument[] Список инструментов
     * @throws TIException
     */
    public function getStocks($tickers = null)
    {
        $response = $this->sendRequest("/market/stocks", "GET");
        return $this->setUpLists($response, $tickers);
    }

    /**
     * Получение списка облигаций
     *
     * @param array $tickers filter tickers
     *
     * @return TIInstrument[]
     * @throws TIException
     */
    public function getBonds($tickers = null)
    {
        $response = $this->sendRequest("/market/bonds", "GET");
        return $this->setUpLists($response, $tickers);
    }

    /**
     * Получение списка ETF
     *
     * @param array $tickers filter ticker
     *
     * @return TIInstrument[]
     * @throws TIException
     */
    public function getEtfs($tickers = null)
    {
        $response = $this->sendRequest("/market/etfs", "GET");
        return $this->setUpLists($response, $tickers);
    }

    /**
     * Получение списка валют
     *
     * @param array $tickers filter ticker
     *
     * @return TIInstrument[]
     * @throws TIException
     */
    public function getCurrencies($tickers = null)
    {
        $currencies = [];
        $response = $this->sendRequest("/market/currencies", "GET");

        foreach ($response->getPayload()->instruments as $instrument) {
            if ($tickers === null || in_array($instrument->ticker, $tickers)) {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $curr = new TIInstrument(
                    $instrument->figi,
                    $instrument->ticker,
                    null,
                    $instrument->minPriceIncrement,
                    $instrument->lot,
                    $currency,
                    $instrument->name,
                    $instrument->type
                );
                $currencies[] = $curr;
            }
        }
        return $currencies;
    }


    /**
     * Получение инструмента по тикеру
     *
     * @param string $ticker
     *
     * @return TIInstrument
     * @throws TIException
     */
    public function getInstrumentByTicker($ticker)
    {
        $response = $this->sendRequest(
            "/market/search/by-ticker",
            "GET",
            ["ticker" => $ticker]
        );

        $currency = TICurrencyEnum::getCurrency(
            $response->getPayload()->instruments[0]->currency
        );
        $isin = (isset($response->getPayload()->instruments[0]->isin)) ? $response->getPayload(
        )->instruments[0]->isin : null;
        return new TIInstrument(
            $response->getPayload()->instruments[0]->figi,
            $response->getPayload()->instruments[0]->ticker,
            $isin,
            $response->getPayload()->instruments[0]->minPriceIncrement,
            $response->getPayload()->instruments[0]->lot,
            $currency,
            $response->getPayload()->instruments[0]->name,
            $response->getPayload()->instruments[0]->type
        );
    }

    /**
     * Получение инструмента по FIGI
     *
     * @param string $figi
     *
     * @return TIInstrument
     * @throws TIException
     */
    public function getInstrumentByFigi($figi)
    {
        $response = $this->sendRequest(
            "/market/search/by-figi",
            "GET",
            ["figi" => $figi]
        );

        $currency = TICurrencyEnum::getCurrency($response->getPayload()->currency);

        $isin = (isset($response->getPayload()->isin)) ? $response->getPayload()->isin : null;
        return new TIInstrument(
            $response->getPayload()->figi,
            $response->getPayload()->ticker,
            $isin,
            $response->getPayload()->minPriceIncrement,
            $response->getPayload()->lot,
            $currency,
            $response->getPayload()->name,
            $response->getPayload()->type
        );
    }

    /**
     * Получение исторического стакана
     *
     * @param string $figi
     * @param int $depth
     * @return TIOrderBook
     * @throws TIException
     */
    public function getHistoryOrderBook($figi, $depth = 1)
    {
        if ($depth < 1) {
            $depth = 1;
        }
        if ($depth > 20) {
            $depth = 20;
        }
        $response = $this->sendRequest(
            "/market/orderbook",
            "GET",
            [
                'figi' => $figi,
                'depth' => $depth,
            ]
        );

        return $this->setUpOrderBook($response->getPayload());
    }

    /**
     * Получение исторических свечей
     * default figi = AAPL
     * default from 7Days ago
     * default to now
     * default interval 15 min
     *
     * @param string $figi
     * @param string $from
     * @param string $to
     * @param string $interval
     * @return TICandle[]
     * @throws TIException
     */
    public function getHistoryCandles($figi, $from, $to, $interval)
    {
        $fromDate = new DateTime();
        $fromDate->add(new DateInterval('P7D'));
        $toDate = new DateTime();

        $response = $this->sendRequest(
            "/market/candles",
            "GET",
            [
                'figi' => empty($figi) ? 'AAPL' : $figi,
                'from' => empty($from) ? $fromDate->format('c') : $from,
                'to' => empty($to) ? $toDate->format('c') : $to,
                'interval' => empty($interval) ? TIIntervalEnum::MIN15 : $interval
            ]
        );
        $array = [];
        foreach ($response->getPayload()->candles as $candle) {
            $array [] = $this->setUpCandle($candle);
        }
        return $array;
    }


    /**
     * Получение текущих аккаунтов пользователя
     *
     * @return TIAccount[]
     * @throws TIException
     */
    public function getAccounts()
    {
        $response = $this->sendRequest("/user/accounts", "GET");
        $accounts = [];
        foreach ($response->getPayload()->accounts as $index => $account) {
            $accounts [] = new TIAccount(
                $account->brokerAccountType,
                $account->brokerAccountId
            );
        }
        return $accounts;
    }


    /**
     * Получить портфель клиента
     *
     * @param TIAccount|null $account
     *
     * @return TIPortfolio
     * @throws TIException
     */
    public function getPortfolio(TIAccount $account = null)
    {
        $currs = [];
        $params = [];
        if ($account) {
            $params = ['brokerAccountId' => $account->getBrokerAccountId()];
        }
        $response = $this->sendRequest(
            "/portfolio/currencies",
            "GET",
            $params
        );

        foreach ($response->getPayload()->currencies as $currency) {
            $ticurrency = TICurrencyEnum::getCurrency($currency->currency);

            $curr = new TIPortfolioCurrency(
                $currency->balance,
                $ticurrency
            );
            $currs[] = $curr;
        }

        $instrs = [];
        $response = $this->sendRequest("/portfolio", "GET", $params);

        foreach ($response->getPayload()->positions as $position) {
            $expectedYeildCurrency = null;
            $expectedYeildValue = null;
            if (isset($position->expectedYield)) {
                $expectedYeildCurrency = TICurrencyEnum::getCurrency(
                    $position->expectedYield->currency
                );
                $expectedYeildValue = $position->expectedYield->value;
            }

            $isin = (isset($position->isin)) ? $position->isin : null;
            $instr = new TIPortfolioInstrument(
                $position->figi,
                $position->ticker,
                $isin,
                $position->instrumentType,
                $position->balance,
                $position->lots,
                $expectedYeildValue,
                $expectedYeildCurrency
            );
            $instrs[] = $instr;
        }

        return new TIPortfolio($currs, $instrs);
    }

    /**
     * Создание лимитной заявки
     *
     * @param string $figi
     * @param int $lots
     * @param TIOperationEnum $operation
     * @param double $price
     *
     * @param null $brokerAccountId
     * @return TIOrder
     * @throws TIException
     */
    public function sendOrder($figi, $lots, $operation, $price, $brokerAccountId = null)
    {
        $req_body = json_encode(
            (object)[
                "lots" => $lots,
                "operation" => $operation,
                "price" => $price,
            ]
        );
        $response = $this->sendRequest(
            "/orders/limit-order",
            "POST",
            [
                "figi" => $figi,
                "brokerAccountId" => $brokerAccountId
            ],
            $req_body
        );

        return $this->setUpOrder($response, $figi);
    }

    /**
     * Создание рыночной заявки
     *
     *
     * @param string $figi
     * @param int $lots
     * @param TIOperationEnum $operation
     *
     * @param null $brokerAccountId
     * @return TIOrder
     * @throws TIException
     */
    public function sendMarketOrder($figi, $lots, $operation, $brokerAccountId = null)
    {
        $req_body = json_encode(
            (object)[
                "lots" => $lots,
                "operation" => $operation,
            ]
        );
        $response = $this->sendRequest(
            "/orders/market-order",
            "POST",
            [
                "figi" => $figi,
                "brokerAccountId" => $brokerAccountId
            ],
            $req_body
        );
        return $this->setUpOrder($response, $figi);
    }

    /**
     * Отменить заявку
     *
     * @param string $orderId Номер заявки
     *
     * @return string status
     * @throws TIException
     */
    public function cancelOrder($orderId)
    {
        $response = $this->sendRequest(
            "/orders/cancel",
            "POST",
            ["orderId" => $orderId]
        );
        //var_dump($response);

        return $response->getStatus();
    }

    /**
     * @param null $orderIds
     * @return array
     * @throws TIException
     */
    public function getOrders($orderIds = null)
    {
        $orders = [];
        $response = $this->sendRequest("/orders", "GET");
        foreach ($response->getPayload() as $order) {
            if ($orderIds === null || in_array($order->orderId, $orderIds)) {
                $ord = new TIOrder(
                    $order->orderId,
                    TIOperationEnum::getOperation($order->operation),
                    $order->status,
                    null, // rejected
                    $order->requestedLots,
                    $order->executedLots,
                    null, // comm currency
                    null, // comm value
                    $order->figi,
                    $order->type,
                    ''
                );
                $orders[] = $ord;
            }
        }
        return $orders;
    }

    /**
     *
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param string $figi
     * @param TIAccount|null $account
     *
     * @return TIOperation[]
     * @throws TIException
     */
    public function getOperations($fromDate, $toDate, $figi = null, $account = null)
    {
        $operations = [];
        $response = $this->sendRequest(
            "/operations",
            "GET",
            [
                "from" => $fromDate->format("c"),
                "to" => $toDate->format("c"),
                "figi" => $figi,
                "brokerAccountId" => $account ? $account->getBrokerAccountId() : $account,
            ]
        );

        foreach ($response->getPayload()->operations as $operation) {
            $trades = [];
            foreach ((empty($operation->trades) ? [] : $operation->trades) as $operationTrade)
            {
                $trades[] = new TIOperationTrade(
                    empty($operationTrade->tradeId) ? null : $operationTrade->tradeId,
                    empty($operationTrade->date) ? null : $operationTrade->date,
                    empty($operationTrade->price) ? null : $operationTrade->price,
                    empty($operationTrade->quantity) ? null : $operationTrade->quantity
                );
            }
            $commissionCurrency = (isset($operation->commission)) ? TICurrencyEnum::getCurrency(
                $operation->commission->currency
            ) : null;
            $commissionValue = (isset($operation->commission)) ? $operation->commission->value : null;
            try {
                $dateTime = new DateTime($operation->date);
            } catch (Exception $e) {
                throw new TIException('Can not create DateTime from operations');
            }
            $opr = new TIOperation(
                $operation->id,
                $operation->status,
                $trades,
                new TICommission($commissionCurrency, $commissionValue),
                TICurrencyEnum::getCurrency($operation->currency),
                $operation->payment,
                empty($operation->price) ? null : $operation->price,
                empty($operation->quantity) ? null : $operation->quantity,
                empty($operation->figi) ? null : $operation->figi,
                empty($operation->instrumentType) ? null : $operation->instrumentType,
                $operation->isMarginCall,
                $dateTime,
                TIOperationEnum::getOperation(
                    empty($operation->operationType) ? null : $operation->operationType
                )
            );
            $operations[] = $opr;
        }
        return $operations;
    }

    /**
     * Отправка запроса на API
     *
     * @param string $action
     * @param string $method
     * @param array $req_params
     * @param string $req_body
     *
     * @return TIResponse
     * @throws TIException
     */
    private function sendRequest(
        $action,
        $method,
        $req_params = [],
        $req_body = null
    ) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url . $action);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        if (count($req_params) > 0) {
            curl_setopt(
                $curl,
                CURLOPT_URL,
                $this->url . $action . '?' . http_build_query(
                    $req_params
                )
            );
        }

        if ($method !== "GET") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $req_body);
        }

        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type:application/json',
                'Authorization: Bearer ' . $this->token,
            ]
        );

        $out = curl_exec($curl);
        $res = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($res !== 200) {
            switch ($res) {
                case 401: 
                    $error_message = "Authorization error";
                    break;
                case 429:  
                    $error_message = "Too Many Requests";
                    break;
                default: 
                    $error_message = "Unkown error";
                    break;
            }
            throw new TIException ($error_message, $res);
        }
        return new TIResponse($out);
    }

    /**
     * @throws TIException
     */
    private function wsConnect()
    {
        try {
            $this->wsClient = new Client(
                "wss://api-invest.tinkoff.ru/openapi/md/v1/md-openapi/ws",
                [
                    "timeout" => 60,
                    "headers" => ["authorization" => "Bearer {$this->token}"],
                ]
            );
        } catch (Exception $e) {
            throw new TIException(
                "Can't connect to stream API. " . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }


    /**
     * @param $figi
     * @param $interval
     * @param string $action
     * @throws TIException
     */
    private function candleSubscribtion($figi, $interval, $action = "subscribe")
    {
        $request = '{
                        "event": "candle:' . $action . '",
                        "figi": "' . $figi . '",
                        "interval": "' . $interval . '"
                    }';
        if (!$this->wsClient->isConnected()) {
            $this->wsConnect();
        }
        try {
            $this->wsClient->send($request);
        } catch (BadOpcodeException $e) {
            throw new TIException('Can not send websocket request errorMessage' . $e->getMessage());
        }
    }

    /**
     * Получить свечу
     *
     * @param string $figi
     * @param string $interval
     *
     * @return TICandle
     * @throws TIException
     */
    public function getCandle($figi, $interval)
    {
        $this->candleSubscribtion($figi, $interval);
        $response = $this->wsClient->receive();
        $this->candleSubscribtion($figi, $interval, "unsubscribe");
        $json = json_decode($response);
        if (empty($json)) {
            throw new TIException('Got empty response for Candle');
        }
        return $this->setUpCandle($json->payload);
    }

    /**
     * @param $figi
     * @param $depth
     * @param string $action
     * @throws TIException
     */
    private function orderbookSubscribtion($figi, $depth, $action = "subscribe")
    {
        $request = '{
                        "event": "orderbook:' . $action . '",
                        "figi": "' . $figi . '",
                        "depth": ' . $depth . '
                    }';
        if (!$this->wsClient->isConnected()) {
            $this->wsConnect();
        }
        try {
            $this->wsClient->send($request);
        } catch (BadOpcodeException $e) {
            throw new TIException('Can not send websocket request errorMessage' . $e->getMessage());
        }
    }

    /**
     * Получить стакан
     *
     * @param string $figi
     * @param int $depth
     *
     * @return TIOrderBook
     * @throws TIException
     */
    public function getOrderBook($figi, $depth = 1)
    {
        if ($depth < 1) {
            $depth = 1;
        }
        if ($depth > 20) {
            $depth = 20;
        }
        $this->orderbookSubscribtion($figi, $depth);
        $response = $this->wsClient->receive();
        $this->orderbookSubscribtion($figi, $depth, "unsubscribe");
        $json = json_decode($response);
        if (empty($json)) {
            throw new TIException('Got empty response for OrderBook');
        }
        return $this->setUpOrderBook($json->payload);
    }

    /**
     * @param $figi
     * @param string $action
     * @throws TIException
     */
    private function instrumentInfoSubscribtion($figi, $action = "subscribe")
    {
        $request = '{
                        "event": "instrument_info:' . $action . '",
                        "figi": "' . $figi . '"
                    }';
        if (!$this->wsClient->isConnected()) {
            $this->wsConnect();
        }
        try {
            $this->wsClient->send($request);
        } catch (BadOpcodeException $e) {
            throw new TIException('Can not send websocket request errorMessage' . $e->getMessage());
        }
    }

    /**
     * Get Instrument info
     *
     * @param string $figi
     *
     * @return TIInstrumentInfo
     * @throws TIException
     */
    public function getInstrumentInfo($figi)
    {
        $this->instrumentInfoSubscribtion($figi);
        $response = $this->wsClient->receive();
        $this->instrumentInfoSubscribtion($figi, "unsubscribe");
        $json = json_decode($response);
        if (empty($json)) {
            throw new TIException('Got empty response for InstrumentInfo');
        }

        return $this->setUpInstrumentInfo($json->payload);
    }


    /**
     * @param $figi
     * @param $interval
     * @throws TIException
     */
    public function subscribeGettingCandle($figi, $interval)
    {
        $this->candleSubscribtion($figi, $interval);
    }

    /**
     * @param $figi
     * @param $depth
     * @throws TIException
     */
    public function subscribeGettingOrderBook($figi, $depth)
    {
        $this->orderbookSubscribtion($figi, $depth);
    }

    /**
     * @param $figi
     * @throws TIException
     */
    public function subscribeGettingInstrumentInfo($figi)
    {
        $this->instrumentInfoSubscribtion($figi);
    }

    /**
     * @param $figi
     * @param $interval
     * @throws TIException
     */
    public function unsubscribeGettingCandle($figi, $interval)
    {
        $this->candleSubscribtion($figi, $interval, "unsubscribe");
    }

    /**
     * @param $figi
     * @param $depth
     * @throws TIException
     */
    public function unsubscribeGettingOrderBook($figi, $depth)
    {
        $this->orderbookSubscribtion($figi, $depth, "unsubscribe");
    }

    /**
     * @param $figi
     * @throws TIException
     */
    public function unsubscribeGettingInstrumentInfo($figi)
    {
        $this->instrumentInfoSubscribtion($figi, "unsubscribe");
    }


    /**
     * @param $callback
     * @param int $max_response
     * @param int $max_time_sec
     */
    public function startGetting(
        $callback,
        $max_response = 10,
        $max_time_sec = 60
    ) {
        $this->startGetting = true;
        $this->response_now = 0;
        $this->response_start_time = time();
        while (true) {
            $response = $this->wsClient->receive();
            $json = json_decode($response);
            if (!isset($json->event) || $json === null) {
                continue;
            }
            try {
                switch ($json->event) {
                    case "candle" :
                        $object = $this->setUpCandle($json->payload);
                        break;
                    case "orderbook" :
                        $object = $this->setUpOrderBook($json->payload);
                        break;
                    case "instrument_info" :
                        $object = $this->setUpInstrumentInfo($json->payload);
                        break;
                }
                if (!empty($object)) {
                    call_user_func($callback, $object);
                }
            } catch (TIException $e) {
                //TODO: add Exception to logger
            }
            $this->response_now++;
            if ($this->startGetting === false || ($max_response !== null && $this->response_now >= $max_response) || ($max_time_sec !== null && time(
                    ) > $this->response_start_time + $max_time_sec)) {
                break;
            }
        }
    }


    /**
     *
     */
    public function stopGetting()
    {
        $this->startGetting = false;
    }


    /**
     * @param $payload
     * @return TIOrderBook
     */
    private function setUpOrderBook($payload)
    {
        return new TIOrderBook(
            empty($payload->depth) ? null : $payload->depth,
            empty($payload->bids) ? null : $payload->bids,
            empty($payload->asks) ? null : $payload->asks,
            empty($payload->figi) ? null : $payload->figi,
            empty($payload->tradeStatus) ? null : $payload->tradeStatus,
            empty($payload->minPriceIncrement) ? null : $payload->minPriceIncrement,
            empty($payload->faceValue) ? null : $payload->faceValue,
            empty($payload->lastPrice) ? null : $payload->lastPrice,
            empty($payload->closePrice) ? null : $payload->closePrice,
            empty($payload->limitUp) ? null : $payload->limitUp,
            empty($payload->limitDown) ? null : $payload->limitDown
        );
    }

    /**
     * @param $payload
     * @return TIInstrumentInfo
     */
    private function setUpInstrumentInfo($payload)
    {
        $object = new TIInstrumentInfo(
            $payload->trade_status,
            $payload->min_price_increment,
            $payload->lot,
            $payload->figi
        );
        if (isset($payload->accrued_interest)) {
            $object->setAccrued_interest(
                $payload->accrued_interest
            );
        }
        if (isset($payload->limit_up)) {
            $object->setLimit_up($payload->limit_up);
        }
        if (isset($payload->limit_down)) {
            $object->setLimit_down($payload->limit_down);
        }
        return $object;
    }


    /**
     * @param $payload
     * @return TICandle
     * @throws TIException
     */
    private function setUpCandle($payload)
    {
        try {
            $datetime = new DateTime($payload->time);
        } catch (Exception $e) {
            throw new TIException('Can not create DateTime for Candle');
        }
        return new TICandle(
            $payload->o,
            $payload->c,
            $payload->h,
            $payload->l,
            $payload->v,
            $datetime,
            TICandleIntervalEnum::getInterval(
                $payload->interval
            ),
            $payload->figi
        );
    }

    /**
     * @param TIResponse $response
     * @param null|array $tickers
     * @return array
     */
    private function setUpLists($response, $tickers = null)
    {
        $array = [];
        foreach ($response->getPayload()->instruments as $instrument) {
            if ($tickers === null || in_array($instrument->ticker, $tickers)) {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);
                $minPriceIncrement = (isset($instrument->minPriceIncrement)) ? $instrument->minPriceIncrement : null;

                $stock = new TIInstrument(
                    empty($instrument->figi) ? null : $instrument->figi,
                    empty($instrument->ticker) ? null : $instrument->ticker,
                    empty($instrument->isin) ? null : $instrument->isin,
                    $minPriceIncrement,
                    empty($instrument->lot) ? null : $instrument->lot,
                    $currency,
                    empty($instrument->name) ? null : $instrument->name,
                    empty($instrument->type) ? null : $instrument->type
                );
                $array[] = $stock;
            }
        }
        return $array;
    }

    /**
     * @param TIResponse $response
     * @param string $figi
     * @return TIOrder
     */
    private function setUpOrder($response, $figi)
    {
        $payload = $response->getPayload();
        $commisionValue = (isset($payload->commission)) ? $payload->commission->value : null;
        $commisionCurrency = (isset($payload->commission)) ? TICurrencyEnum::getCurrency(
            $payload->commission->currency
        ) : null;
        $rejectReason = (isset($payload->rejectReason)) ? $payload->rejectReason : null;

        return new TIOrder(
            empty($payload->orderId) ? null : $payload->orderId,
            TIOperationEnum::getOperation($payload->operation),
            empty($payload->status) ? null : $payload->status,
            $rejectReason,
            empty($payload->requestedLots) ? null : $payload->requestedLots,
            empty($payload->executedLots) ? null : $payload->executedLots,
            $commisionCurrency,
            $commisionValue,
            $figi,
            null, // type
            empty($payload->message) ? null :$payload->message
        );
    }

}
