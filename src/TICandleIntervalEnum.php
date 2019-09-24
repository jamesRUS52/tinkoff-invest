<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TICandleIntervalEnum
 *
 * @author james
 */
abstract class TICandleIntervalEnum {
    
    //put your code here
    const MIN1 = "1min";
    const MIN2 = "2min";
    const MIN3 = "3min";
    const MIN5 = "5min";
    const MIN10 = "10min";
    const MIN15 = "15min";
    const MIN30 = "30min";
    const HOUR1 = "hour";
    const HOUR2 = "2hour";
    const HOUR4 = "4hour";
    const DAY = "day";
    const WEEK = "week";
    const MONTH = "month";
    
    /**
     * 
     * @param string $interval
     * @return TICandleIntervalEnum
     */
    public static function getInterval($interval)
    {
        switch ($interval)
        {
            case "1min" : return TICandleIntervalEnum::MIN1;
                break;
            case "2min" : return TICandleIntervalEnum::MIN2;
                break;
            case "3min" : return TICandleIntervalEnum::MIN3;
                break;
            case "5min" : return TICandleIntervalEnum::MIN5;
                break;
            case "10min" : return TICandleIntervalEnum::MIN10;
                break;
            case "15min" : return TICandleIntervalEnum::MIN15;
                break;
            case "30min" : return TICandleIntervalEnum::MIN30;
                break;
            case "hour" : return TICandleIntervalEnum::HOUR1;
                break;
            case "2hour" : return TICandleIntervalEnum::HOUR2;
                break;
            case "4hour" : return TICandleIntervalEnum::HOUR4;
                break;
            case "day" : return TICandleIntervalEnum::DAY;
                break;
            case "week" : return TICandleIntervalEnum::WEEK;
                break;
            case "month" : return TICandleIntervalEnum::MONTH;
                break;
            default : return null;
        }
    }
}
