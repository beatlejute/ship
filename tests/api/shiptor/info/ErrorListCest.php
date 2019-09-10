<?php

use \tests\api\abstracts\info\errorList as errorListTest;

class shiptorerrorListCest extends errorListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'shiptor';

        $this->errorCode = "_1";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
