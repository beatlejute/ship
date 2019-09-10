<?php

use \tests\api\abstracts\info\tariffList as tariffListTest;

class cdektariffListCest extends tariffListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'cdek';

        $this->tariffName = "Доставка до ПВЗ";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
