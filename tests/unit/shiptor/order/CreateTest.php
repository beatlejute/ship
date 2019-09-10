<?php

namespace shiptor;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\create as createTest;



class shiptorOrderCreateTest extends createTest {

    protected function _before() {
        $this->namespace = 'shiptor';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
              "jsonrpc": "2.0",
              "result": {
                  "packages": [';
        foreach ($obj['CreatedSendings'] as $id => $val) {
            if($id) $tmp .= ',';
            $tmp .= '{
                          "id": '.$val['InvoiceNumber'].',
                          "external_id": "'.$val['orderNumber'].'",
                          "status": "new",
                          "tracking_number": "'.$val['InvoiceNumber'].'",
                          "external_tracking_number": null,
                          "created_at": "2016-10-25 00:11:25",
                          "weight": 10,
                          "length": 10,
                          "width": 10,
                          "height": 10,
                          "cod": 1000,
                          "declared_cost": 1000,
                          "no_gather": false,
                          "delivery_time": "с 10:00 до 13:00",
                          "label_url": "http://shiptor.local/package/90492f19feeb8c139dbf79ee4c8c101e974c470900ae738d50e73abc384cc31d/label.png",
                          "photo": [
                            {
                              "default": "http://shiptor.ru/uploads/package_photo/1a/90/1a9068ec3b36ac813c01bf4c0c5e9df629331b7a.png",
                              "medium": "http://shiptor.ru/uploads/package_photo/1a/90/1a9068ec3b36ac813c01bf4c0c5e9df629331b7a_medium.png",
                              "mini": "http://shiptor.ru/uploads/package_photo/1a/90/1a9068ec3b36ac813c01bf4c0c5e9df629331b7a_mini.png"
                            },
                            {
                              "default": "http://shiptor.ru/uploads/package_photo/ab/78/ab7815f2b40d3c45ab5905fb61db9923b86b1571.png",
                              "medium": "http://shiptor.ru/uploads/package_photo/ab/78/ab7815f2b40d3c45ab5905fb61db9923b86b1571_medium.png",
                              "mini": "http://shiptor.ru/uploads/package_photo/ab/78/ab7815f2b40d3c45ab5905fb61db9923b86b1571_mini.png"
                            }
                          ],
                          "departure": {
                              "shipping_method": {
                                  "id": 11,
                                  "name": "PickPoint",
                                  "category": "delivery-point",
                                  "courier": "pickpoint"
                              },
                              "address": {
                                 "receiver": "Иванов Сергей Петрович",
                                  "name": "null",
                                  "surname": "null",
                                  "patronymic": "null",
                                  "email": "mail@example.com",
                                  "phone": "+7 800 200-06-00",
                                  "country_code": "RU",
                                  "postal_code": "394063",
                                  "administrative_area": "Воронежская обл.",
                                  "settlement": "г. Воронеж",
                                  "street": "60 Aрмии",
                                  "house": "27",
                                  "apartment": "5",
                                  "kladr_id": "36000001000"
                              },
                              "delivery_point": {
                                  "id": 4603,
                                  "courier": "pickpoint",
                                  "address": "Воронежская обл., Воронеж, 60 Армии ул., д. 27",
                                  "phones": [],
                                  "trip_description": "Пункт выдачи",
                                  "work_schedule": "пн-пт:11:00-19:00, сб:11:00-14:00, вс:NODAY",
                                  "shipping_days": null,
                                  "cod": true,
                                  "card": true,
                                  "gps_location": {
                                      "latitude": "51.711665",
                                      "longitude": "39.163429"
                                  },
                                  "kladr_id": "36000001000",
                                  "shipping_methods": [
                                      11,
                                      21,
                                      44
                                  ],
                                  "limits": {
                                      "max_weight": {
                                          "value": 15,
                                          "unit": "kg"
                                      },
                                      "max_dimensions": {
                                          "length": 60,
                                          "width": 36,
                                          "height": 36,
                                          "unit": "cm"
                                      }
                                  }
                              },
                              "cashless_payment": false,
                              "comment": null
                          },
                          "products": [],
                          "pick_up": null,
                          "checkpoints": [],
                          "history": []
                      }';
        }
        $tmp .= ']
              },
              "id": "JsonRpcClient.js"
            }';

        return json_decode($tmp, true);

    }

}

?>
