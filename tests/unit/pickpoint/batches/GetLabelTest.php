<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getLabel as getLabelTest;



class pickpointBatchesGetLabelTest extends getLabelTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {

        //return $obj;

    }

}

?>
