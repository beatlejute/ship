<?php

namespace cdek;

use \Codeception\Stub\Expected;


use \tests\unit\abstracts\info\ParcelShopsList as parcelShopsListTest;



class cdekInfoParcelShopsListTest extends parcelShopsListTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\info';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8"?>
			<PvzList weightMax="1">';
        foreach ($obj as $id => $val) {

            $tmp .= '<Pvz 
                        Code="'.$val["Number"].'" 
                        Name="На Вольсковой" 
                        CountryCode="1" 
                        CountryName="Россия" 
                        RegionCode="81" 
                        RegionName="'.$val["Region"].'" 
                        CityCode="44" 
                        City="'.$val['CitiName'].'" 
                        WorkTime="'.$val["WorkTime"].'" 
                        Address="'.$val["Address"].'" 
                        FullAddress="'.$val["Address"].'" 
                        Phone="'.$val["Phone"].'" 
                        Note="'.$val["Phone"].'" 
                        coordX="'.$val["Longitude"].'" 
                        coordY="'.$val["Latitude"].'" 
                        Type="PVZ" 
                        ownerCode="'.$val["OutDescription"].'" 
                        IsDressingRoom="есть" 
                        HaveCashless="'.($val["Card"] ? 'да' : 'нет').'" 
                        AllowedCod="есть" 
                        NearestStation="Sibirskaya" 
                        MetroStation=" " 
                        Site="https://dfdfdf.com" >
					<WorkTimeY day="1" periods="09:00/20:00" />
					<WorkTimeY day="2" periods="09:00/20:00" />
					<WorkTimeY day="3" periods="09:00/20:00" />
					<WorkTimeY day="4" periods="09:00/20:00" />
					<WorkTimeY day="5" periods="09:00/20:00" />
					<WorkTimeY day="6" periods="09:00/20:00" />
					<WorkTimeY day="7" periods="09:00/20:00" />
					<WeightLimit WeightMin="0" WeightMax="'.$val["MaxWeight"].'" SizeMax="'.$val["MaxSize"].'"></WeightLimit>
				</Pvz>';

        }
        $tmp .= '</PvzList>';

        $response = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        return json_decode($response, true);

    }

}

?>
