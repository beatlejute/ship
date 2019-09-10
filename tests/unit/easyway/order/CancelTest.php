<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\cancel as cancelTest;



class easywayOrderCancelTest extends cancelTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
                    "data": [{
                         "id": "744171",
                         "cancel": true,
                         "descr": "Отменена"
                    }]
                }';


        return json_decode($tmp, true);

    }

}

?>
