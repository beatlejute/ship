<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getLabel as getLabelTest;



class easywayBatchesGetLabelTest extends getLabelTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {



    }

}

?>
