<?php

use \tests\api\abstracts\info\CityList as cityListTest;

class dpdCityListCest extends cityListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'dpd';
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
