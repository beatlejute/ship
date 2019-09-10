<?php

use \tests\api\abstracts\order\getCost as getCostTest;

class easywayGetCostCest extends getCostTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'easyway';

        $this->PVZ = "d0ae4ccd-54de-11e7-80cf-00155d233d13";
        $this->tariffName = "Авто до двери";
        $this->tariffName2 = "Авто до ПВЗ";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
