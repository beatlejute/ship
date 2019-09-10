<?php

use \tests\api\abstracts\info\errorList as errorListTest;

class pickpointerrorListCest extends errorListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'pickpoint';

        $this->errorCode = "_1";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
