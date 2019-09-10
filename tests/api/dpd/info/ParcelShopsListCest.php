<?php

use \tests\api\abstracts\info\parcelShopsList as parcelShopsListTest;

class dpdparcelShopsListCest extends parcelShopsListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'dpd';

        $this->PVZnumber = "001H";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
