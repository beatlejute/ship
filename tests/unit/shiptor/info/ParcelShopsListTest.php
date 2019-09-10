<?php

namespace shiptor;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\ParcelShopsList as parcelShopsListTest;



class shiptorInfoParcelShopsListTest extends parcelShopsListTest {

    protected function _before() {
        $this->namespace = 'shiptor';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        $tmp["jsonrpc"] = "2.0";
        foreach ($obj as $id => $val) {

            $tmp["result"][$id]["id"] = $val["Number"];
            $tmp["result"][$id]["courier"] = $val["OwnerName"];
            $tmp["result"][$id]["address"] = $val["Address"];
            $tmp["result"][$id]['phones'][0] = $val["Phone"];
            $tmp["result"][$id]['trip_description'] = $val["OutDescription"];
            $tmp["result"][$id]['work_schedule'] = $val["WorkTime"];
            $tmp["result"][$id]['card'] = $val["Card"];
            $tmp["result"][$id]['gps_location']['latitude'] = $val["Latitude"];
            $tmp["result"][$id]['gps_location']['longitude'] = $val["Longitude"];
            $tmp["result"][$id]['limits']['MaxWeight'] = $val["MaxWeight"];
            $tmp["result"][$id]['limits']['MaxSize'] = $val["MaxSize"];
            $tmp["result"][$id]['prepare_address']['administrative_area'] = $val["Region"];
            $tmp["result"][$id]['prepare_address']['settlement'] = $val["CitiName"];
            $tmp["result"][$id]['prepare_address']['street'] = $val["Street"];
            $tmp["result"][$id]['prepare_address']['house'] = $val["House"];
            $tmp["result"][$id]['prepare_address']['postal_code'] = $val["Index"];

        }
        $tmp["id"] = "JsonRpcClient.js";

        return $tmp;

    }

}

?>
