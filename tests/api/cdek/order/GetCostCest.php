<?php

use \tests\api\abstracts\order\getCost as getCostTest;

class cdekgetCostCest extends getCostTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'cdek';

        $this->PVZ = "NUG4";
        $this->tariffName = "Доставка до двери";
        $this->tariffName2 = "Доставка до ПВЗ";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
