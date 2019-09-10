<?php

namespace dpd;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\getInfo as getInfoTest;



class dpdOrderGetInfoTest extends getInfoTest {

    protected function _before() {
        $this->namespace = 'dpd';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {


        $tmp['states'][0]['newState'] = $obj['status']['State'];
        $tmp['states'][0]['transitionTime'] = $obj['status']['ChangeDT'];

        $tmp['states'][0]['dpdParcelNr'] = $obj['info']['InvoiceNumber'];
        $tmp['states'][0]['clientOrderNr'] = $obj['info']['SenderInvoiceNumber'];
        $tmp['states'][0]['orderCost'] = $obj['info']['Sum'];
        $tmp['docDate'] = $obj['info']['CreateDate'];
        $tmp['states'][0]['pickupDate'] = $obj['info']['StorageDate'];


        return $tmp;

    }

}

?>
