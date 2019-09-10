<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\getInfo as getInfoTest;



class easywayOrderGetInfoTest extends getInfoTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '[
            {
            "clientId": "'.$obj['info']['SenderInvoiceNumber'].'",
            "date": "'.$obj['status']['ChangeDT'].'",
            "status": "'.$obj['status']['StateMessage'].'",
            "arrivalPlanDateTime": "2017-05-22T15:00:00",
            "dateOrder": "2017-05-15T15:08:00",
            "sender": "Московская обл.",
            "receiver": "Самара",
            "carrierTrackNumber": "000029766",
            "address": "Терминал, Склад ПЭК, г. Ярославль, проспект Октября, 93",
            "deliveryType": "Терминал",
            "phone": "",
            "statusCode": "'.$obj['status']['State'].'",
            "id": "'.$obj['info']['InvoiceNumber'].'",
            "date": "'.$obj['info']['CreateDate'].'",
            "regionFrom": "Московская обл.",
            "regionTo": "Самара",
            "addressFrom": "Россия, 0, г Москва, Огородный проезд, 20, К3",
            "addressTo": "ПВЗ, Склад ПЭК, г. Самара, ул. Береговая, д.36",
            "weight": 11.34,
            "volume": 0.074784,
            "length": 41,
            "width": 40,
            "height": 45.6,
            "accessedCost": 5650,
            "cargoCost": 5650,
            "recipient": "'.$obj['info']['FIO'].'",
            "recipientPhone": "79053049601",
            "total": '.$obj['info']['Sum'].',
            "deliveryCost": 398,
            "outsideZoneCost": 0,
            "insuranceCost": 0,
            "addServicesCost": 0
            }
        ]';

        return json_decode($tmp, true);

    }

}

?>
