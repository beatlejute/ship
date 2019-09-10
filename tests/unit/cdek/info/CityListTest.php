<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\info\CityList as cityListTest;



class cdekInfoCityListTest extends cityListTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8"?>
			<PvzList weightMax="1">';
        foreach ($obj as $id => $val) {

            $tmp .= '<Pvz Code="MSK64" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="'.($val["RegionName"] ? $val["RegionName"].($val["RegionTypeShort"] ? ' '.$val["RegionTypeShort"].'.' : '') : '').'" CityCode="'.$val['Id'].'" City="'.$val['name'].'" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="есть" HaveCashless="нет" AllowedCod="есть" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
					<WorkTimeY day="1" periods="09:00/20:00" />
					<WorkTimeY day="2" periods="09:00/20:00" />
					<WorkTimeY day="3" periods="09:00/20:00" />
					<WorkTimeY day="4" periods="09:00/20:00" />
					<WorkTimeY day="5" periods="09:00/20:00" />
					<WorkTimeY day="6" periods="09:00/20:00" />
					<WorkTimeY day="7" periods="09:00/20:00" />
					<WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
				</Pvz>';

        }
        $tmp .= '</PvzList>';

        $response = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        return json_decode($response, true);

    }

}

?>
