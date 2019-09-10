<?php

use \tests\api\abstracts\info\tariffList as tariffListTest;

class shiptortariffListCest extends tariffListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'shiptor';

        $this->tariffName = "Shiptor Курьер";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
