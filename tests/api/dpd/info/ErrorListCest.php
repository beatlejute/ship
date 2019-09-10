<?php

use \tests\api\abstracts\info\errorList as errorListTest;

class dpderrorListCest extends errorListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'dpd';

        $this->errorCode = "_1";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
