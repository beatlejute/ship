<?php

use \tests\api\abstracts\info\CityList as cityListTest;

class shiptorCityListCest extends cityListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'shiptor';
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
