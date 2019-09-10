<?php

use \tests\api\abstracts\info\CityList as cityListTest;

class easywayСityListCest extends cityListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'easyway';
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
