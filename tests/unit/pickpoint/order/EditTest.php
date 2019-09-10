<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\edit as editTest;



class pickpointOrderEditTest extends editTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        return $obj;

    }

}

?>
