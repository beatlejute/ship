<?php

use \tests\api\abstracts\info\CityList as cityListTest;

class pickpointСityListCest extends cityListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'pickpoint';
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
