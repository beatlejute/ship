<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\ParcelShopsList as parcelShopsListTest;



class pickpointInfoParcelShopsListTest extends parcelShopsListTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        foreach ($obj as $id => $val) {

            $tmp[$id] = $val;
            $tmp[$id]["Status"] = 2;
            $tmp[$id]['AmountTo'] = 'Без ограничений';

        }

        return $tmp;

    }

}

?>
