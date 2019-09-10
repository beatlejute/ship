<?php

use \tests\api\abstracts\info\tariffList as tariffListTest;

class easywaytariffListCest extends tariffListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'easyway';

        $this->tariffName = "Авто до двери";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
