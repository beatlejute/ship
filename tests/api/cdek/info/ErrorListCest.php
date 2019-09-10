<?php

use \tests\api\abstracts\info\errorList as errorListTest;

class cdekerrorListCest extends errorListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'cdek';

        $this->errorCode = "_1";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
