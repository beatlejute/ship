<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getOrderList as getOrderListTest;



class pickpointBatchesGetOrderListTest extends getOrderListTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {

        return $obj;

    }

}

?>
