<?php

namespace dpd;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\cancel as cancelTest;



class dpdOrderCancelTest extends cancelTest {

    protected function _before() {
        $this->namespace = 'dpd';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp['orderNumberInternal'] = 'НФ-1234';
        $tmp['orderNum'] = '744171';
        $tmp['status'] = 'Cancelled';

        return $tmp;

    }

}

?>
