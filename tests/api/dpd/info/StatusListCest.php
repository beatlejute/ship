<?php

use \tests\api\abstracts\info\statusList as statusListTest;

class dpdstatusListCest extends statusListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'dpd';

        $this->State = "NewOrderByDPD";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
