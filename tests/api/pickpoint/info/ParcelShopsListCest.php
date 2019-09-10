<?php

use \tests\api\abstracts\info\parcelShopsList as parcelShopsListTest;

class pickpointparcelShopsListCest extends parcelShopsListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'pickpoint';

        $this->PVZnumber = "6601-001";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
