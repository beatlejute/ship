<?php

use \tests\api\abstracts\info\parcelShopsList as parcelShopsListTest;

class cdekparcelShopsListCest extends parcelShopsListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'cdek';

        $this->PVZnumber = "MSK17";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
