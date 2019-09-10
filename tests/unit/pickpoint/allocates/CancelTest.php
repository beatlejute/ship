<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\allocates\cancel as cancelTest;



class pickpointAllocatesCancelTest extends cancelTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\allocates';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
            "Canceled":	true
        }';

        return json_decode($tmp, true);

    }

}

?>
