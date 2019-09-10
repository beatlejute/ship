<?php

use \tests\api\abstracts\info\tariffList as tariffListTest;

class dpdtariffListCest extends tariffListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'dpd';

        $this->tariffName = "DPD ECONOMY";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
