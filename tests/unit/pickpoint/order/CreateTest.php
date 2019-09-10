<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\create as createTest;



class pickpointOrderCreateTest extends createTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        return $obj;

    }

}

?>
