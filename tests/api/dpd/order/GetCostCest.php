<?php

use \tests\api\abstracts\order\getCost as getCostTest;

class dpdgetCostCest extends getCostTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'dpd';

        $this->PVZ = "032L";
        $this->tariffName = "DPD Online Classic";
        $this->tariffName2 = "DPD Online Classic to point";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
