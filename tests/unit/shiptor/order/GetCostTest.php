<?php

namespace shiptor;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\GetCost as getCostTest;



class shiptorOrderGetCostTest extends getCostTest {

    protected function _before() {
        $this->namespace = 'shiptor';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp["result"]["jsonrpc"] = "2.0";
        $tmp["result"]["request"]["length"] = 10;
        $tmp["result"]["request"]["width"] = 10;
        $tmp["result"]["request"]["height"] = 10;
        $tmp["result"]["request"]["weight"] = 10;
        $tmp["result"]["request"]["cod"] = 10;
        $tmp["result"]["request"]["declared_cost"] = 10;
        $tmp["result"]["request"]["kladr_id"] = "01000001000";
        $tmp["result"]["request"]["courier"] = "dpd";
        foreach ($obj as $id => $val) {
            $tmp["result"]["methods"][$id]['status'] = "ok";
            $tmp["result"]["methods"][$id]['method']['id'] = $val['id'];
            $tmp["result"]["methods"][$id]['method']['name'] = $val['name'];
            //$tmp["result"]["methods"][$id]['method']['category'] = $val['name'];
            switch ($val['mode']) {
                case 'С-С':
                    $tmp["result"]["methods"][$id]['method']['category'] = 'delivery-point';
                    break;
                case 'С-Д':
                    $tmp["result"]["methods"][$id]['method']['category'] = 'to-door';
                    break;
                case 'Д-Д':
                    $tmp["result"]["methods"][$id]['method']['category'] = 'door-to-door';
                    break;
                case 'Д-С':
                    $tmp["result"]["methods"][$id]['method']['category'] = 'door-to-delivery-point';
                    break;
            }
            $tmp["result"]["methods"][$id]['cost']['services']['service'] = "shipping";
            $tmp["result"]["methods"][$id]['cost']['services']['sum'] = 1858.5;
            $tmp["result"]["methods"][$id]['cost']['services']['currency'] = "RUB";
            $tmp["result"]["methods"][$id]['cost']['services']['readable'] = "1 858,50 ₽";
            $tmp["result"]["methods"][$id]['cost']['total']['sum'] = $val['cost'];
            $tmp["result"]["methods"][$id]['cost']['total']['currency'] = "RUB";
            $tmp["result"]["methods"][$id]['cost']['total']['readable'] = $val['cost']." ₽";
            $tmp["result"]["methods"][$id]['days'] = $val['DPMin'];
        }
        $tmp["result"]["id"] = "JsonRpcClient.js";

        return $tmp;

    }

}

?>
