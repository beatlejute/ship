<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\GetCost as getCostTest;



class easywayOrderGetCostTest extends getCostTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '[';
        foreach ($obj as $id => $val) {

            if($id) $tmp .=  ',';

            $tmp .=  '{
                        "deliveryType": '.$val['id'].',
                        "total": '.$val['cost'].',
                        "mode": "'.$val['mode'].'",
                        "name": "'.$val['name'].'",
                        "estDeliveryTime": {
                         "min": "1",
                         "max": "2"
                         }
                        }';

        }
        $tmp .=  ']';

        return json_decode($tmp, true);

    }

}

?>
