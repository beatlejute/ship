<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\cancel as cancelTest;



class pickpointOrderCancelTest extends cancelTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
            "Result": true
        }';

        return json_decode($tmp, true);

    }

}

?>
