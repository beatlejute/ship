<?php

use \tests\api\abstracts\info\errorList as errorListTest;

class easywayerrorListCest extends errorListTest
{
    public function _before(ApiTester $I)
    {
        $this->namespace = 'easyway';

        $this->errorCode = "_1";
    }

    // tests
    /*public function tryToTest(ApiTester $I)
    {
    }*/
}
