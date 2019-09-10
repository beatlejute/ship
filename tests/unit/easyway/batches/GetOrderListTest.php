<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getOrderList as getOrderListTest;



class easywayBatchesGetOrderListTest extends getOrderListTest {

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
                    "data": [';

        foreach ($obj['Invoices'] as $id => $val) {

            if ($id) $tmp .= ',';

            $tmp .= '
            {
                "clientId": "' . $val['CustomerNumber'] . '",
                "date": "2017-05-20T11:58:42",
                "status": "На ПВЗ",
                "arrivalPlanDateTime": "2017-05-22T15:00:00",
                "dateOrder": "2017-05-15T15:08:00",
                "sender": "Московская обл.",
                "receiver": "Самара",
                "carrierTrackNumber": "000029766",
                "address": "Терминал, Склад ПЭК, г. Ярославль, проспект Октября, 93",
                "deliveryType": "Терминал",
                "phone": "",
                "id": "' . $val['Number'] . '",
                "statusCode": "' . $val['Encloses']['Statuses'][0]['Code'] . '"
            }';

        }

         $tmp .= ']}';

        $tmp = json_decode($tmp, true);
        return $tmp;

    }

}

?>
