<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getLabel as getLabelTest;



class cdekBatchesGetLabelTest extends getLabelTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {



    }

}

?>
