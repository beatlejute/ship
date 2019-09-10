<?php

namespace shiptor;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\CityList as cityListTest;



class shiptorInfoCityListTest extends cityListTest {

    protected function _before() {
        $this->namespace = 'shiptor';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        $tmp["result"]["jsonrpc"] = "2.0";
        $tmp["result"]["count"] = 1191;
        $tmp["result"]["page"] = 0;
        $tmp["result"]["per_page"] = 0;
        $tmp["result"]["pages"] = 0;
        foreach ($obj as $id => $val) {
            $tmp["result"]["settlements"][$id]['kladr_id'] = $val['kladr'];
            $tmp["result"]["settlements"][$id]["name"] = $val['name'];
            $tmp["result"]["settlements"][$id]["type"] = "Город";
            $tmp["result"]["settlements"][$id]["type_short"] = "г";
            $tmp["result"]["settlements"][$id]['parents'] = [];
            if($val["RegionName"]) {
                $tmp["result"]["settlements"][$id]['parents'][0]['name'] = $val["RegionName"];
                $tmp["result"]["settlements"][$id]['parents'][0]['type_short'] = $val["RegionTypeShort"];
                $tmp["result"]["settlements"][$id]['parents'][0]['kladr_id'] = "";
            }
        }
        $tmp["result"]["id"] = "JsonRpcClient.js";

        return $tmp;

    }

}

?>
