<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace jamesRUS52\TinkoffInvest;

/**
 * Description of TISite
 *
 * @author james
 */
abstract class TIIntervalEnum {
    //put your code here
    const MIN1 = '1min';
    const MIN2 = '2min';
    const MIN3 = '3min';
    const MIN5 = '5min';
    const MIN10 = '10min';
    const MIN15 = '15min';
    const MIN30 = '30min';
    const HOUR = 'hour';
    const DAY = 'day';
    const WEEK = 'week';
    const MONTH = 'month';

    const DAY1 = "1day";
    const DAY7 = "7days";
    const DAY14 = "14days";
    const DAY30 = "30days";

}
