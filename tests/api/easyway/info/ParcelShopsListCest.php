<?php

use \tests\api\abstracts\info\parcelShopsList as parcelShopsListTest;

class easywayparcelShopsListCest extends parcelShopsListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'easyway';

        $this->PVZnumber = "bd4eceff-54dc-11e7-80cf-00155d233d13";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
