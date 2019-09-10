<?php

namespace dpd;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\CityList as cityListTest;



class dpdInfoCityListTest extends cityListTest {

    protected function _before() {
        $this->namespace = 'dpd';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        if($obj) foreach ($obj as $id => $val) {

            $tmp[$id]['cityName'] = $val['name'];
            if(array_key_exists('Id', $val)) $tmp[$id]['cityId'] = $val['Id'];
            if(array_key_exists('RegionName', $val)) $tmp[$id]['regionName'] = $val['RegionName'];
            if(array_key_exists('RegionId', $val)) $tmp[$id]['regionCode'] = $val['RegionId'];
            if(array_key_exists('kladr', $val)) $tmp[$id]['cityCode'] = $val['kladr'];

        } else return $obj;

        return $tmp;

    }

}

?>
