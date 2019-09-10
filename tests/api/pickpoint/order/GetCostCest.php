<?php

use \tests\api\abstracts\order\getCost as getCostTest;

class pickpointOrderGetCostCest extends getCostTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'pickpoint';

        $this->PVZ = "4205-018";
        $this->tariffName = "Доставка до двери";
        $this->tariffName2 = "Доставка до ПВЗ";
    }

    // tests
    public function getCostTest(\ApiTester $I)
    {
    }
}
