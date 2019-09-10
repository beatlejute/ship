<?php

namespace dpd;

use \Codeception\Stub\Expected;


use \tests\unit\abstracts\info\ParcelShopsList as parcelShopsListTest;



class dpdInfoParcelShopsListTest extends parcelShopsListTest {

    protected function _before() {
        $this->namespace = 'dpd';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        if($obj) foreach ($obj as $id => $val) {

            if(array_key_exists('Number', $val)) $tmp[$id]['code'] = $val['Number'];
            if(array_key_exists('OwnerName', $val)) $tmp[$id]['brand'] = $val['OwnerName'];
            if(array_key_exists('OutDescription', $val)) $tmp[$id]['address']['descript'] = $val['OutDescription'];
            $tmp[$id]['schedule']['timetable']['weekDays'] = '';
            if(array_key_exists('WorkTime', $val)) $tmp[$id]['schedule']['timetable']['workTime'] = $val['WorkTime'];
            if(array_key_exists('Card', $val) && $val['Card']) $tmp[$id]['schedule']['operation'] = 'PaymentByBankCard';
            if(array_key_exists('Latitude', $val)) $tmp[$id]['geoCoordinates']['latitude'] = $val['Latitude'];
            if(array_key_exists('Longitude', $val)) $tmp[$id]['geoCoordinates']['longitude'] = $val['Longitude'];
            if(array_key_exists('MaxWeight', $val)) $tmp[$id]['limits']['maxWeight'] = $val['MaxWeight'];
            if(array_key_exists('MaxSize', $val)) $tmp[$id]['limits']['dimensionSum'] = $val['MaxSize'];
            if(array_key_exists('Region', $val)) $tmp[$id]['address']['regionName'] = $val['Region'];
            if(array_key_exists('CitiId', $val)) $tmp[$id]['address']['cityId'] = $val['CitiId'];
            if(array_key_exists('CitiName', $val)) $tmp[$id]['address']['cityName'] = $val['CitiName'];
            if(array_key_exists('House', $val)) $tmp[$id]['address']['houseNo'] = $val['House'];
            if(array_key_exists('BuildingType', $val)) $tmp[$id]['address']['building'] = $val['BuildingType'];
            if(array_key_exists('PostCode', $val)) $tmp[$id]['address']['index'] = $val['PostCode'];
            if(array_key_exists('Street', $val)) $tmp[$id]['address']['street'] = $val['Street'];

        } else return $obj;

        $tmp['parcelShop'] = $tmp;
        return $tmp;

    }

}

?>
