<?php

namespace easyway;

use \Codeception\Stub\Expected;


use \tests\unit\abstracts\info\ParcelShopsList as parcelShopsListTest;



class easywayInfoParcelShopsListTest extends parcelShopsListTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        foreach ($obj as $id => $val) {

            $tmp[$id]["city"] = $val["CitiName"];
            $tmp[$id]["address"] = $val["Address"];
            $tmp[$id]["lat"] = $val["Latitude"];
            $tmp[$id]["lng"] = $val["Longitude"];
            $tmp[$id]["guid"] = $val["Number"];
            $tmp[$id]["partner"] = $val["OwnerName"];
            $tmp[$id]["schedule"] = $val["WorkTime"];
            $tmp[$id]["phone"] = $val["Phone"];
            $tmp[$id]["MaxWeight"] = $val["MaxWeight"];
            $tmp[$id]["MaxSize"] = $val["MaxSize"];
            $tmp[$id]["Card"] = $val["Card"];
            $tmp[$id]["fiasRegionId"] = ($val["Region"] == 'Свердловская обл.') ? '92b30014-4d52-4e2e-892d-928142b924bf' : '63ed1a35-4be6-4564-a1ec-0c51f7383314';
            $tmp[$id]["partner"] = $val["OwnerName"];

        }

        return $tmp;

    }

}

?>
