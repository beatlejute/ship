<?php

use \tests\api\abstracts\info\CityList as cityListTest;

class cdekСityListCest extends cityListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'cdek';
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
