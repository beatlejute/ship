<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\allocates\courier as courierTest;



class pickpointAllocatesCourierTest extends courierTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\allocates';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
            "CourierRequestRegistred":	true
        }';

        return json_decode($tmp, true);

    }

}

?>
