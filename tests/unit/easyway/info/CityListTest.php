<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\CityList as cityListTest;



class easywayInfoCityListTest extends cityListTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        foreach ($obj as $id => $val) {

            $tmp[$id]["city"] = $val['name'];
            $tmp[$id]["address"] = "Россия, Ярославль, проспект Октября, 93";
            $tmp[$id]["lat"] = 57.661655;
            $tmp[$id]["lng"] = 39.841954;
            $tmp[$id]["office"] = false;
            $tmp[$id]["guid"] = "352845f6-a017-11e6-80c7-000d3a2542c4";
            $tmp[$id]["partner"] = "ПЭК";
            $tmp[$id]["schedule"] = "Пн.-Пт. с 9-19, сб. с 10-16, вс.- вых";
            $tmp[$id]["phone"] = "";

        }

        return $tmp;

    }

}

?>
