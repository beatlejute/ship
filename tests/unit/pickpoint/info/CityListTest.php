<?php

namespace pickpoint;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\CityList as cityListTest;



class pickpointInfoCityListTest extends cityListTest {

    protected function _before() {
        $this->namespace = 'pickpoint';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        foreach ($obj as $id => $val) {

            $tmp[$id]["Id"] = 992;
            $tmp[$id]["Name"] = $val['name'];
            $tmp[$id]["NameEng"] = "Moscow";
            $tmp[$id]["Owner_Id"] = 0;
            $tmp[$id]["RegionName"] = $val["RegionName"] ? $val["RegionName"].($val["RegionTypeShort"] ? ' '.$val["RegionTypeShort"].'.' : '') : '';

        }

        return $tmp;

    }

}

?>
