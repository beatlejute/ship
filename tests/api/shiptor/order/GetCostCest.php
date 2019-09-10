<?php

use \tests\api\abstracts\order\getCost as getCostTest;

class shiptorgetCostCest extends getCostTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'shiptor';

        $this->PVZ = 233;
        $this->tariffName = "DPD Курьер Авто";
        $this->tariffName2 = "DPD Самовывоз";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
