<?php

namespace dpd;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\GetCost as getCostTest;



class dpdOrderGetCostTest extends getCostTest {

    protected function _before() {
        $this->namespace = 'dpd';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        foreach ($obj as $id => $val) {
            $tmp[$id]['serviceCode'] = $val['id'];
            $tmp[$id]['serviceName'] = $val['name'];
            $tmp[$id]['days'] = $val['DPMin'];
            $tmp[$id]['cost'] = $val['cost'];
            $tmp[$id]['mode'] = $val['mode'];
        }

        return $tmp;

    }

}

?>
