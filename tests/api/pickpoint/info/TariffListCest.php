<?php

use \tests\api\abstracts\info\tariffList as tariffListTest;

class pickpointtariffListCest extends tariffListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'pickpoint';

        $this->tariffName = "Доставка до ПВЗ";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
