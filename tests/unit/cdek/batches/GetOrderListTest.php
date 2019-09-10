<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getOrderList as getOrderListTest;



class cdekBatchesGetOrderListTest extends getOrderListTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8"?>
                    <StatusReport DateFirst="2000-12-31T17:00:00+00:00" DateLast="2018-08-10T08:55:52+00:00" >';

        foreach ($obj['Invoices'] as $id => $val) {
            if ($id) $tmp .= ',';

            $tmp .= '<Order ActNumber="" Number="'.$val['CustomerNumber'].'" DispatchNumber="'.$val['Number'].'"  DeliveryDate="2018-04-06T13:33:27+03:00" RecipientName="Руслан Альбертович" >
                        <Status Date="2018-04-06T10:33:42+00:00" Code="'.$val['Encloses']['Statuses'][0]['Code'].'" Description="Вручен" CityCode="1081" CityName="Нальчик">
                            <State Date="2018-03-21T14:54:13+00:00" Code="1" Description="Создан" CityCode="44" CityName="Москва" />
                            <State Date="2018-03-21T17:32:32+00:00" Code="3" Description="Принят на склад отправителя" CityCode="44" CityName="Москва" />
                            <State Date="2018-03-21T17:35:12+00:00" Code="6" Description="Выдан на отправку в г.-отправителе" CityCode="44" CityName="Москва" />
                            <State Date="2018-03-21T23:00:12+00:00" Code="7" Description="Сдан перевозчику в г.-отправителе" CityCode="44" CityName="Москва" />
                            <State Date="2018-03-21T23:36:53+00:00" Code="21" Description="Отправлен в г.-транзит" CityCode="44" CityName="Москва" />
                            <State Date="2018-03-22T19:01:19+00:00" Code="22" Description="Встречен в г.-транзите" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-22T22:18:47+00:00" Code="13" Description="Принят на склад транзита" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-22T22:18:47+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-22T22:19:44+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-22T22:59:19+00:00" Code="8" Description="Отправлен в г.-получатель" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-24T15:11:53+00:00" Code="17" Description="Возвращен на склад транзита" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-24T15:11:53+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-25T21:25:02+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-25T22:32:47+00:00" Code="8" Description="Отправлен в г.-получатель" CityCode="438" CityName="Ростов-на-Дону" />
                            <State Date="2018-03-26T11:13:27+00:00" Code="10" Description="Принят на склад доставки" CityCode="1081" CityName="Нальчик" />
                            <State Date="2018-04-06T05:28:09+00:00" Code="11" Description="Выдан на доставку" CityCode="1081" CityName="Нальчик" />
                            <State Date="2018-04-06T10:33:42+00:00" Code="4" Description="Вручен" CityCode="1081" CityName="Нальчик" />
                        </Status>
                        <Reason Code="" Description="" Date=""></Reason>
                        <DelayReason Code="" Description="" Date="" ></DelayReason>
                        <Call>
                            <CallGood>
                                <Good Date="2018-03-26T12:50:31+00:00" DateDeliv="2018-04-06" />
                                <Good Date="2018-03-26T12:50:37+00:00" DateDeliv="2018-04-06" />
                            </CallGood>
                        </Call>
                    </Order>';
        }

        $tmp .= '</StatusReport>';


        $tmp = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        $tmp = json_decode($tmp, true);

        return $tmp;

    }

}

?>
