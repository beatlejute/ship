<?php

use \tests\api\abstracts\info\statusList as statusListTest;

class pickpointstatusListCest extends statusListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'pickpoint';

        $this->State = "101";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
