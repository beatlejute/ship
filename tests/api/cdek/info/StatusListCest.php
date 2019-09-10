<?php

use \tests\api\abstracts\info\statusList as statusListTest;

class cdekstatusListCest extends statusListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'cdek';

        $this->State = "1";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
