<?php

namespace dpd;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\create as createTest;



class dpdOrderCreateTest extends createTest {

    protected function _before() {
        $this->namespace = 'dpd';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        foreach ($obj['CreatedSendings'] as $id => $val) {

            $tmp[$id]['orderNumberInternal'] = $val['orderNumber'];
            $tmp[$id]['orderNum'] = $val['InvoiceNumber'];
            $tmp[$id]['status'] = "new";

        }

        return $tmp;

    }

}

?>
