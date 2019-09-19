<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TIClient
 *
 * @author james
 */
class TIClient {
    //put your code here
    private $token;
    private $url;
    
    /**
     * 
     * @param string $token token from tinkoff.ru for specific site
     * @param TISiteEnum $site site name (sandbox or real exchange)
     */
    function __construct($token,$site) {
        $this->token = $token;
        $this->url = $site;
    }
    
    
    /**
     * Удаление всех позиций в песочнице
     * @return string status
     */
    public function sbClear()
    {
        $response = $this->sendRequest("/sandbox/clear","POST");
        return $response->status;
    }
    
    /**
     * Регистрация клиента в sandbox
     * @return string status
     */
    public function sbRegister()
    {
        $response = $this->sendRequest("/sandbox/register","POST");
        return $response->status;
    }
    
    /**
     * Выставление баланса по инструментным позициям
     * @param double $balance
     * @param string $figi
     * @return string status
     */
    public function sbPositionBalance($balance, $figi)
    {
        $request = array("figi"=>$figi,"balance"=>$balance);
        $request_body = json_encode($request, JSON_NUMERIC_CHECK);
        $response = $this->sendRequest("/sandbox/positions/balance","POST",[],$request_body);
        return $response->status;
    }
    
    /**
     * Выставление баланса по инструментным позициям
     * @param double $balance
     * @param TICurrencyEnum $currency
     * @return string status
     */
    public function sbCurrencyBalance($balance, $currency = TICurrencyEnum::RUB)
    {
        $request = array("currency"=>$currency,"balance"=>$balance);
        $request_body = json_encode($request, JSON_NUMERIC_CHECK);
        $response = $this->sendRequest("/sandbox/currencies/balance","POST",[],$request_body);
        return $response->status;
    }
    
    /**
     * Получение списка акций
     * @param array $tickers Ticker Filter
     * @return \jamesRUS52\TinkoffInvest\TIInstrument[] Список инструментов
     */
    public function getStocks($tickers=null)
    {
        $stocks = array();
        $response = $this->sendRequest("/market/stocks","GET");
        
        foreach ($response->payload->instruments as $instrument)
        {
            if ($tickers === null || in_array($instrument->ticker, $tickers))
            {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $stock = new TIInstrument(
                        $instrument->figi,
                        $instrument->ticker,
                        $instrument->isin,
                        $instrument->minPriceIncrement,
                        $instrument->lot,
                        $currency
                        );
                 $stocks[] = $stock;       
            }
        }
        return $stocks;
    }
    
    /**
     * Получение списка облигаций
     * @param array $tickers filter tickers
     * @return \jamesRUS52\TinkoffInvest\TIInstrument[]
     */
    public function getBonds($tickers=null)
    {
        $bonds = array();
        $response = $this->sendRequest("/market/bonds","GET");
        
        foreach ($response->payload->instruments as $instrument)
        {
            if ($tickers === null || in_array($instrument->ticker, $tickers))
            {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $bond = new TIInstrument(
                        $instrument->figi,
                        $instrument->ticker,
                        $instrument->isin,
                        $instrument->minPriceIncrement,
                        $instrument->lot,
                        $currency
                        );
                 $bonds[] = $bond;       
            }
        }
        return $bonds;
    }
    
    /**
     * Получение списка ETF
     * @param array $tickers filter ticker
     * @return \jamesRUS52\TinkoffInvest\TIInstrument[]
     */
    public function getEtfs($tickers=null)
    {
        $etfs = array();
        $response = $this->sendRequest("/market/etfs","GET");
        
        foreach ($response->payload->instruments as $instrument)
        {
            if ($tickers === null || in_array($instrument->ticker, $tickers))
            {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $etf = new TIInstrument(
                        $instrument->figi,
                        $instrument->ticker,
                        $instrument->isin,
                        $instrument->minPriceIncrement,
                        $instrument->lot,
                        $currency
                        );
                 $etfs[] = $etf;       
            }
        }
        return $etfs;
    }
    
    /**
     * Получение списка валют
     * @param array $tickers filter ticker
     * @return \jamesRUS52\TinkoffInvest\TIInstrument
     */
    public function getCurrencies($tickers=null)
    {
        $currencies = array();
        $response = $this->sendRequest("/market/currencies","GET");
        
        foreach ($response->payload->instruments as $instrument)
        {
            if ($tickers === null || in_array($instrument->ticker, $tickers))
            {
                $currency = TICurrencyEnum::getCurrency($instrument->currency);

                $curr = new TIInstrument(
                        $instrument->figi,
                        $instrument->ticker,
                        NULL,
                        $instrument->minPriceIncrement,
                        $instrument->lot,
                        $currency
                        );
                 $currencies[] = $curr;       
            }
        }
        return $currencies;
    }

    /**
     * Получение инструмента по тикеру
     * @param string $ticker
     * @return \jamesRUS52\TinkoffInvest\TIInstrument
     */
    public function getInstrumentByTicker($ticker)
    {
        $stocks = array();
        $response = $this->sendRequest("/market/search/by-ticker","GET",["ticker"=>$ticker]);
        
        $currency = TICurrencyEnum::getCurrency($response->payload->instruments[0]->currency);

        $instrument = new TIInstrument(
                $response->payload->instruments[0]->figi,
                $response->payload->instruments[0]->ticker,
                $response->payload->instruments[0]->isin,
                $response->payload->instruments[0]->minPriceIncrement,
                $response->payload->instruments[0]->lot,
                $currency
                );
        
        return $instrument;
    }
    
    /**
     * Получение инструмента по FIGI
     * @param string $figi
     * @return \jamesRUS52\TinkoffInvest\TIInstrument
     */
    public function getInstrumentByFigi($figi)
    {
        $stocks = array();
        $response = $this->sendRequest("/market/search/by-figi","GET",["figi"=>$figi]);
        
        $currency = TICurrencyEnum::getCurrency($response->payload->currency);

        $instrument = new TIInstrument(
                $response->payload->figi,
                $response->payload->ticker,
                $response->payload->isin,
                $response->payload->minPriceIncrement,
                $response->payload->lot,
                $currency
                );
        
        return $instrument;
    }
    
    /** 
     * Получить портфель клиента
     * @return TIPortfolio
     */
    public function getPortfolio()
    {
        $currs = array();
        $response = $this->sendRequest("/portfolio/currencies","GET");
        
        foreach ($response->payload->currencies as $currency)
        {
            $ticurrency = TICurrencyEnum::getCurrency($currency->currency);
            
            $curr = new TIPortfolioCurrency(
                    $currency->balance,
                    $ticurrency
                    );
             $currs[] = $curr;       
        }
        
        $instrs = array();
        $response = $this->sendRequest("/portfolio","GET");
        
        foreach ($response->payload->positions as $position)
        {
            $expectedYeildCurrency = null;
            $expectedYeildValue = null;
            //var_dump($position);
            if (isset($position->expectedYield))
            {
                $expectedYeildCurrency = TICurrencyEnum::getCurrency($position->expectedYield->currency);
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
     * @return \jamesRUS52\TinkoffInvest\TIOrder
     */
    public function sendOrder($figi,$lots,$operation,$price)
    {
        $req_body = json_encode((object)array("lots"=>$lots,"operation"=>$operation,"price"=>$price));
        $response = $this->sendRequest("/orders/limit-order","POST",["figi"=>$figi],$req_body);
        //var_dump($response);
        
        $commisionValue = (isset($response->payload->commision)) ? $response->payload->commision->value : null;
        $commisionCurrency = (isset($response->payload->commision)) ? TICurrencyEnum::getCurrency($response->payload->commision->currency) : null;
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
     * @param string $orderId Номер заявки
     * @return string status
     */
    public function cancelOrder($orderId)
    {
        $response = $this->sendRequest("/orders/cancel","POST",["orderId"=>$orderId]);
        //var_dump($response);
        
        return $response->status;
    }
    
    public function getOrders($orderIds = null)
    {
        $orders = array();
        $response = $this->sendRequest("/orders","GET");
        
        foreach ($response->payload as $order)
        {
            if ($orderIds === null || in_array($order->orderId, $orderIds))
            {
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
     * @return \jamesRUS52\TinkoffInvest\TIOperation[]
     */
    public function getOperations($fromDate, $intervalDays, $figi = null)
    {
        $operations = array();
        $response = $this->sendRequest("/operations","GET",["from"=>$fromDate->format("Y-m-d"),"interval"=>$intervalDays,"figi"=>$figi]);
        foreach ($response->payload->operations as $operation)
        {
            $trades = (isset($operation->trades)) ? $operation->trades : [];
            $commisionCurrency = (isset($operation->commision)) ? TICurrencyEnum::getCurrency($operation->commision->currency) : null;
            $commisionValue = (isset($operation->commision)) ? $operation->commision->value : null;
            $opr = new TIOperation(
                    $operation->id,
                    $operation->status,
                    $trades,
                    $commisionCurrency,
                    $commisionValue,
                    TICurrencyEnum::getCurrency($operation->currency),
                    $operation->payment,
                    $operation->price,
                    $operation->quantity,
                    $operation->figi,
                    $operation->instrumentType,
                    $operation->isMarginCall,
                    new \DateTime($operation->date),
                    TIOperationEnum::getOperation($operation->operationType)
                    );
            $operations[] = $opr;       

        }
        return $operations;
        
    }

    /**
     * Отправка запроса на API
     * @param string $action
     * @param string $method
     * @param array $req_params
     * @param string $req_body
     * @return array json array from api
     * @throws TIException
     */
    private function sendRequest($action, $method, $req_params=[], $req_body=null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url.$action);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        
        if (count($req_params)>0)
            curl_setopt($curl, CURLOPT_URL, $this->url.$action.'?'.http_build_query($req_params));
        
        if ($method !== "GET")
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $req_body);
        }
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization: Bearer '.$this->token
                ));
        
        $out = curl_exec($curl);
        $res = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        $result = json_decode($out);
        //print "<BR>".$action."<BR>";
        //var_dump($result);
        if ($res !== 200)
        {
            if ($res == 401)
                $error_message = "Authorization error";
            else
                $error_message = (isset($result->status) && isset($result->payload)) ? $result->status.' '.$result->payload->message : "Unknown error";
            throw new TIException ($error_message, $res);
        }
        
        return $result;
    }
}
