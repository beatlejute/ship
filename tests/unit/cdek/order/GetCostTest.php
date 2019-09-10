<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\GetCost as getCostTest;



class cdekOrderGetCostTest extends getCostTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $val = $obj[0];

        $tmp = '{
            "result": {
                "price":"'.$val['cost'].'",
                "deliveryPeriodMin":"'.$val['DPMin'].'",
                "deliveryPeriodMax":"'.$val['DPMax'].'",
                "deliveryDateMin":"2012-07-28",
                "deliveryDateMax":"2012-07-29",
                "tariffId":"'.$val['id'].'" ,
                "cashOnDelivery":"30000.00"
            }
        }';

        return json_decode($tmp, true);

    }

    public function testReturns() {}

}

?>
