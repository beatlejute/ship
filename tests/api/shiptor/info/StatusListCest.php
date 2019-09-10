<?php

use \tests\api\abstracts\info\statusList as statusListTest;

class shiptorstatusListCest extends statusListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'shiptor';

        $this->State = "new";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
