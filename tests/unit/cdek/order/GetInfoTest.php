<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\getInfo as getInfoTest;



class cdekOrderGetInfoTest extends getInfoTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8" ?>
          <StatusReport DateFirst="2013-07-16T00:00:00">
            <Order ActNumber ="236"
            Number ="' . $obj['info']['SenderInvoiceNumber'] . '"
            DispatchNumber ="' . $obj['info']['InvoiceNumber'] . '"
            DeliveryDate="2013-07-16T14:23:00"
            RecipientName="' . $obj['info']['FIO'] . '">
                <Status Date="2013-07-17T00:00:00"
            Code="' . $obj['status']['State'] . '"
            Description="Вручен" CityCode="270">
                <State Date="2013-07-16T08:12:00" Code="8" Description="Отправлен в г.-получатель" CityCode="44" />
                <State Date="2013-07-16T09:40:00" Code="10" Description="Принят на склад доставки" CityCode="270" />
                <State Date="2013-07-16T14:23:00" Code="4" Description="Вручен" CityCode="270" />
            </Status>
            <Reason Date="2013-07-16T14:23:00" Code="20" Description="Частичная доставка" />
            <Package Number="1">
                <Item WareKey="25000050368" DelivAmount="1"/>
                <Item WareKey="25000348563" DelivAmount="1"/>
            </Package>
           </Order>
         </StatusReport>';

        $tmp = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        $tmp = json_decode($tmp, true);

        return $tmp;

    }

}

?>
