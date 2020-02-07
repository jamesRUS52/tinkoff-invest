<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

use WebSocket\Client;

/**
 * Description of TIClient
 *
 * @author james
 */
class TIClient
{

    //put your code here
    private $token;

    private $url;

    /**
     *
     * @var WebSocket\Client
     */
    private $wsClient;

    private $startGetting = false;

    private $response_now = 0;

    private $response_start_time;

    /**
     *
     * @param string $token token from tinkoff.ru for specific site
     * @param TISiteEnum $site site name (sandbox or real exchange)
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
     */
    public function sbClear()
    {
        $response = $this->sendRequest("/sandbox/clear", "POST");
        return $response->status;
    }

    /**
     * Регистрация клиента в sandbox
     *
     * @return string status
     */
    public function sbRegister()
    {
        $response = $this->sendRequest("/sandbox/register", "POST");
        return $response->status;
    }

    /**
     * Выставление баланса по инструментным позициям
     *
     * @param double $balance
     * @param string $figi
     *
     * @return string status
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
        return $response->status;
    }

    /**
     * Выставление баланса по инструментным позициям
     *
     * @param double $balance
     * @param TICurrencyEnum $currency
     *
     * @return string status
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
        return $response->status;
    }

    /**
     * Получение списка акций
     *
     * @param array $tickers Ticker Filter
     *
     * @return \jamesRUS52\TinkoffInvest\TIInstrument[] Список инструментов
     */
    public function getStocks($tickers = null)
    {
        $stocks = [];
        $response = $this->sendRequest("/market/stocks", "GET");

        foreach ($response->payload->instruments as $instrument) {
            if ($tickers === null || in_array($instrument->ticker, $tickers)) {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $stock = new TIInstrument(
                    $instrument->figi,
                    $instrument->ticker,
                    $instrument->isin,
                    $instrument->minPriceIncrement,
                    $instrument->lot,
                    $currency,
                    $instrument->name
                );
                $stocks[] = $stock;
            }
        }
        return $stocks;
    }

    /**
     * Получение списка облигаций
     *
     * @param array $tickers filter tickers
     *
     * @return \jamesRUS52\TinkoffInvest\TIInstrument[]
     */
    public function getBonds($tickers = null)
    {
        $bonds = [];
        $response = $this->sendRequest("/market/bonds", "GET");

        foreach ($response->payload->instruments as $instrument) {
            if ($tickers === null || in_array($instrument->ticker, $tickers)) {
                $currency = TICurrencyEnum::getCurrency(
                    $instrument->currency
                );
                $minPriceIncrement = (isset($instrument->minPriceIncrement)) ? $instrument->minPriceIncrement : null;
                $bond = new TIInstrument(
                    $instrument->figi,
                    $instrument->ticker,
                    $instrument->isin,
                    $minPriceIncrement,
                    $instrument->lot,
                    $currency,
                    $instrument->name
                );
                $bonds[] = $bond;
            }
        }
        return $bonds;
    }

    /**
     * Получение списка ETF
     *
     * @param array $tickers filter ticker
     *
     * @return \jamesRUS52\TinkoffInvest\TIInstrument[]
     */
    public function getEtfs($tickers = null)
    {
        $etfs = [];
        $response = $this->sendRequest("/market/etfs", "GET");

        foreach ($response->payload->instruments as $instrument) {
            if ($tickers === null || in_array($instrument->ticker, $tickers)) {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $etf = new TIInstrument(
                    $instrument->figi,
                    $instrument->ticker,
                    $instrument->isin,
                    $instrument->minPriceIncrement,
                    $instrument->lot,
                    $currency,
                    $instrument->name
                );
                $etfs[] = $etf;
            }
        }
        return $etfs;
    }

    /**
     * Получение списка валют
     *
     * @param array $tickers filter ticker
     *
     * @return \jamesRUS52\TinkoffInvest\TIInstrument
     */
    public function getCurrencies($tickers = null)
    {
        $currencies = [];
        $response = $this->sendRequest("/market/currencies", "GET");

        foreach ($response->payload->instruments as $instrument) {
            if ($tickers === null || in_array($instrument->ticker, $tickers)) {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $curr = new TIInstrument(
                    $instrument->figi,
                    $instrument->ticker,
                    null,
                    $instrument->minPriceIncrement,
                    $instrument->lot,
                    $currency,
                    $instrument->name
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
     * @return \jamesRUS52\TinkoffInvest\TIInstrument
     */
    public function getInstrumentByTicker($ticker)
    {
        $stocks = [];
        $response = $this->sendRequest(
            "/market/search/by-ticker",
            "GET",
            ["ticker" => $ticker]
        );

        $currency = TICurrencyEnum::getCurrency(
            $response->payload->instruments[0]->currency
        );
        $isin = (isset($response->payload->instruments[0]->isin)) ? $response->payload->instruments[0]->isin : null;
        $instrument = new TIInstrument(
            $response->payload->instruments[0]->figi,
            $response->payload->instruments[0]->ticker,
            $isin,
            $response->payload->instruments[0]->minPriceIncrement,
            $response->payload->instruments[0]->lot,
            $currency,
            $response->payload->instruments[0]->name
        );

        return $instrument;
    }

    /**
     * Получение инструмента по FIGI
     *
     * @param string $figi
     *
     * @return \jamesRUS52\TinkoffInvest\TIInstrument
     */
    public function getInstrumentByFigi($figi)
    {
        $stocks = [];
        $response = $this->sendRequest(
            "/market/search/by-figi",
            "GET",
            ["figi" => $figi]
        );

        $currency = TICurrencyEnum::getCurrency($response->payload->currency);

        $isin = (isset($response->payload->isin)) ? $response->payload->isin : null;
        $instrument = new TIInstrument(
            $response->payload->figi,
            $response->payload->ticker,
            $isin,
            $response->payload->minPriceIncrement,
            $response->payload->lot,
            $currency,
            $response->payload->name
        );

        return $instrument;
    }

    /**
     * Получение текущих аккаунтов пользователя
     *
     * @return \jamesRUS52\TinkoffInvest\TIAccount[]
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function getAccounts()
    {
        $response = $this->sendRequest("/user/accounts", "GET");
        $accounts = [];
        foreach ($response->payload->accounts as $index => $account) {
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
     * @param \jamesRUS52\TinkoffInvest\TIAccount|null $account
     *
     * @return TIPortfolio
     * @throws \jamesRUS52\TinkoffInvest\TIException
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

        foreach ($response->payload->currencies as $currency) {
            $ticurrency = TICurrencyEnum::getCurrency($currency->currency);

            $curr = new TIPortfolioCurrency(
                $currency->balance,
                $ticurrency
            );
            $currs[] = $curr;
        }

        $instrs = [];
        $response = $this->sendRequest("/portfolio", "GET", $params);

        foreach ($response->payload->positions as $position) {
            $expectedYeildCurrency = null;
            $expectedYeildValue = null;
            //var_dump($position);
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
     *
     * @param string $figi
     * @param integer $lots
     * @param \jamesRUS52\TinkoffInvest\TIOperationEnum $operation
     * @param double $price
     *
     * @return \jamesRUS52\TinkoffInvest\TIOrder
     */
    public function sendOrder($figi, $lots, $operation, $price)
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
            ["figi" => $figi],
            $req_body
        );
        //var_dump($response);

        $commisionValue = (isset($response->payload->commision)) ? $response->payload->commision->value : null;
        $commisionCurrency = (isset($response->payload->commision)) ? TICurrencyEnum::getCurrency(
            $response->payload->commision->currency
        ) : null;
        $rejectReason = (isset($response->payload->rejectReason)) ? $response->payload->rejectReason : null;
        return new TIOrder(
            $response->payload->orderId,
            TIOperationEnum::getOperation($response->payload->operation),
            $response->payload->status,
            $rejectReason,
            $response->payload->requestedLots,
            $response->payload->executedLots,
            $commisionCurrency,
            $commisionValue,
            null, // figi
            null // type
        );
    }

    /**
     * Отменить заявку
     *
     * @param string $orderId Номер заявки
     *
     * @return string status
     */
    public function cancelOrder($orderId)
    {
        $response = $this->sendRequest(
            "/orders/cancel",
            "POST",
            ["orderId" => $orderId]
        );
        //var_dump($response);

        return $response->status;
    }

    public function getOrders($orderIds = null)
    {
        $orders = [];
        $response = $this->sendRequest("/orders", "GET");
        var_dump($response);
        foreach ($response->payload as $order) {
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
                    $order->type
                );
                $orders[] = $ord;
            }
        }
        return $orders;
    }

    /**
     *
     * @param \DateTime $fromDate
     * @param \TIIntervalEnum $intervalDays
     * @param string $figi
     *
     * @return \jamesRUS52\TinkoffInvest\TIOperation[]
     */
    public function getOperations($fromDate, $toDate, $figi = null)
    {
        $operations = [];
        $response = $this->sendRequest(
            "/operations",
            "GET",
            [
                "from" => $fromDate->format("c"),
                "to" => $toDate->format("c"),
                "figi" => $figi,
            ]
        );
        foreach ($response->payload->operations as $operation) {
            $trades = new TIOperationTrade(
                empty($operation->trades->tradeId) ? null : $operation->trades->tradeId,
                empty($operation->trades->date) ? null : $operation->trades->date,
                empty($operation->trades->price) ? null : $operation->trades->price,
                empty($operation->trades->quantity) ? null : $operation->trades->quantity
            );
            $commissionCurrency = (isset($operation->commision)) ? TICurrencyEnum::getCurrency(
                $operation->commision->currency
            ) : null;
            $commissionValue = (isset($operation->commision)) ? $operation->commision->value : null;
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
                new \DateTime($operation->date),
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
     * @return array json array from api
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
        $err = curl_error($curl);

        curl_close($curl);

        $result = json_decode($out);
        //print "<BR>".$action."<BR>";
        //var_dump($result);
        if ($res !== 200) {
            if ($res == 401) {
                $error_message = "Authorization error";
            } else {
                $error_message = (isset($result->status) && isset($result->payload)) ? $result->status . ' ' . $result->payload->message : "Unknown error";
            }
            throw new TIException ($error_message, $res);
        }

        return $result;
    }

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
        } catch (\Exception $e) {
            throw new \jamesRUS52\TinkoffInvest\TIException(
                "Can't connect to stream API. " . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }


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
        $this->wsClient->send($request);
    }

    /**
     * Получить свечу
     *
     * @param string $figi
     * @param \jamesRUS52\TinkoffInvest\TICandleIntervalEnum $interval
     *
     * @return \jamesRUS52\TinkoffInvest\TICandle
     */
    public function getCandle($figi, $interval)
    {
        $this->candleSubscribtion($figi, $interval);
        $response = $this->wsClient->receive();
        $this->candleSubscribtion($figi, $interval, "unsubscribe");
        $json = json_decode($response);
        $candle = new TICandle(
            $json->payload->o,
            $json->payload->c,
            $json->payload->h,
            $json->payload->l,
            $json->payload->v,
            new \DateTime($json->payload->time),
            TICandleIntervalEnum::getInterval(
                $json->payload->interval
            ),
            $json->payload->figi
        );
        return $candle;
    }

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
        $this->wsClient->send($request);
    }

    /**
     * Получить стакан
     *
     * @param string $figi
     * @param int $depth
     *
     * @return \jamesRUS52\TinkoffInvest\TIOrderBook
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
        $orderbook = new TIOrderBook(
            $json->payload->depth,
            $json->payload->bids,
            $json->payload->asks,
            $json->payload->figi
        );
        return $orderbook;
    }

    private function instrumentInfoSubscribtion($figi, $action = "subscribe")
    {
        $request = '{
                        "event": "instrument_info:' . $action . '",
                        "figi": "' . $figi . '"
                    }';
        if (!$this->wsClient->isConnected()) {
            $this->wsConnect();
        }
        $this->wsClient->send($request);
    }

    /**
     * Get Instrument info
     *
     * @param string $figi
     *
     * @return \jamesRUS52\TinkoffInvest\TIInstrumentInfo
     */
    public function getInstrumentInfo($figi)
    {
        $this->instrumentInfoSubscribtion($figi);
        $response = $this->wsClient->receive();
        $this->instrumentInfoSubscribtion($figi, "unsubscribe");
        $json = json_decode($response);
        $instrument = new TIInstrumentInfo(
            $json->payload->trade_status,
            $json->payload->min_price_increment,
            $json->payload->lot,
            $json->payload->figi
        );
        if (isset($json->payload->accrued_interest)) {
            $instrument->setAccrued_interest($json->payload->accrued_interest);
        }
        if (isset($json->payload->limit_up)) {
            $instrument->setLimit_up($json->payload->limit_up);
        }
        if (isset($json->payload->limit_down)) {
            $instrument->setLimit_down($json->payload->limit_down);
        }
        return $instrument;
    }


    public function subscribeGettingCandle($figi, $interval)
    {
        $this->candleSubscribtion($figi, $interval);
    }

    public function subscribeGettingOrderBook($figi, $depth)
    {
        $this->orderbookSubscribtion($figi, $depth);
    }

    public function subscribeGettingInstrumentInfo($figi)
    {
        $this->instrumentInfoSubscribtion($figi);
    }

    public function unsubscribeGettingCandle($figi, $interval)
    {
        $this->candleSubscribtion($figi, $interval, "unsubscribe");
    }

    public function unsubscribeGettingOrderBook($figi, $depth)
    {
        $this->orderbookSubscribtion($figi, $depth, "unsubscribe");
    }

    public function unsubscribeGettingInstrumentInfo($figi)
    {
        $this->instrumentInfoSubscribtion($figi, "unsubscribe");
    }


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
            switch ($json->event) {
                case "candle" :
                    $object = new TICandle(
                        $json->payload->o,
                        $json->payload->c,
                        $json->payload->h,
                        $json->payload->l,
                        $json->payload->v,
                        new \DateTime($json->payload->time),
                        TICandleIntervalEnum::getInterval(
                            $json->payload->interval
                        ),
                        $json->payload->figi
                    );
                    break;
                case "orderbook" :
                    $object = new TIOrderBook(
                        $json->payload->depth,
                        $json->payload->bids,
                        $json->payload->asks,
                        $json->payload->figi
                    );
                    break;
                case "instrument_info" :
                    $object = new TIInstrumentInfo(
                        $json->payload->trade_status,
                        $json->payload->min_price_increment,
                        $json->payload->lot,
                        $json->payload->figi
                    );
                    if (isset($json->payload->accrued_interest)) {
                        $object->setAccrued_interest(
                            $json->payload->accrued_interest
                        );
                    }
                    if (isset($json->payload->limit_up)) {
                        $object->setLimit_up($json->payload->limit_up);
                    }
                    if (isset($json->payload->limit_down)) {
                        $object->setLimit_down($json->payload->limit_down);
                    }
                    break;
            }
            call_user_func($callback, $object);

            $this->response_now++;
            if ($this->startGetting === false || ($max_response !== null && $this->response_now >= $max_response) || ($max_time_sec !== null && time(
                    ) > $this->response_start_time + $max_time_sec)) {
                break;
            }
        }
    }

    public function stopGetting()
    {
        $this->startGetting = false;
    }

}
