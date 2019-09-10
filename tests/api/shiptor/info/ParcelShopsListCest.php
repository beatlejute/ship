<?php

use \tests\api\abstracts\info\parcelShopsList as parcelShopsListTest;

class shiptorparcelShopsListCest extends parcelShopsListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'shiptor';

        $this->PVZnumber = 32;
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
