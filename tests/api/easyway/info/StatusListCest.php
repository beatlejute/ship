<?php

use \tests\api\abstracts\info\statusList as statusListTest;

class easywaystatusListCest extends statusListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'easyway';

        $this->State = "9f02eabc-6aa3-11e6-80e9-003048baa05f";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
