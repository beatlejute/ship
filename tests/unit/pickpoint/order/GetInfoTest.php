<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\getInfo as getInfoTest;



class pickpointOrderGetInfoTest extends getInfoTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = $obj['status'];
        $tmp[] = array_merge($tmp, $obj['info']);

        return $tmp;

    }

}

?>
