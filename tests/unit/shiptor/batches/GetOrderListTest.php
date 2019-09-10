<?php

namespace shiptor;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getOrderList as getOrderListTest;



class shiptorBatchesGetOrderListTest extends getOrderListTest {

    protected function _before() {
        $this->namespace = 'shiptor';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
                    "jsonrpc": "2.0",
                    "result": [';

        foreach ($obj['Invoices'] as $id => $val) {

            if($id) $tmp .= ',';

            $tmp .= '{
                            "id": '.$val['Number'].',
                            "external_id": "'.$val['CustomerNumber'].'",
                            "stock": 1,
                            "status": "'.$val['Encloses']['Statuses'][0]['Code'].'",
                            "tracking_number": "RP769787",
                            "external_tracking_number": null,
                            "created_at": "2018-12-03 07:26:51",
                            "weight": 10,
                            "length": 10,
                            "width": 10,
                            "height": 10,
                            "cod": 1000,
                            "declared_cost": 1000,
                            "no_gather": false,
                            "label_url": "https://shiptor.ru/package/dd5beed7bd3b2fe239513eb90d98bdb8719386548a2451f8e70351936a5716dc/label.png",
                            "small_label_url": "https://shiptor.ru/package/dd5beed7bd3b2fe239513eb90d98bdb8719386548a2451f8e70351936a5716dc/print-label?size=1",
                            "cost": {
                                "shipping_cost": "359.90",
                                "cod_service_cost": 40,
                                "compensation_service_cost": 5,
                                "total_cost": 404.9
                            },
                            "departure": {
                                "shipping_method": {
                                    "id": 11,
                                    "name": "PickPoint",
                                    "category": "delivery-point",
                                    "group": "pickpoint",
                                    "courier": "pickpoint",
                                    "comment": "",
                                    "description": null,
                                    "help_url": "https://shiptor.ru/help/common/all-delivery-methods#pick-point"
                                },
                                "address": {
                                    "receiver": null,
                                    "email": "mail@example.com",
                                    "phone": "+7 800 200-06-00",
                                    "country_code": "RU",
                                    "administrative_area": "Воронежская обл",
                                    "settlement": "г Воронеж",
                                    "address_line_1": null,
                                    "marked_as_trash_at": null,
                                    "name": "Сергей",
                                    "surname": "Иванов",
                                    "patronymic": "Петрович",
                                    "postal_code": "394077",
                                    "street": "ул 60 Армии",
                                    "house": "27",
                                    "apartment": "5",
                                    "kladr_id": "36000001000"
                                },
                                "comment": null,
                                "delivery_point": {
                                    "id": 4603,
                                    "courier": "pickpoint",
                                    "address": "Воронежская обл., Воронеж, 60 Армии ул., д. 27",
                                    "phones": [],
                                    "trip_description": "Пункт выдачи заказов находится в жилом доме на пересечение улиц 60 Армии и Бульвар Победы, напротив Торгово-развлекательного центра Арена. Угловой дом, вход со стороны Победы б-р. Остановка 60 Армии ул. Транспорт: Маршрутное такси №15, 27, 27А, 37, 37А, 49М, 54, 58В, 59А, 108А; Автобус №41, 84, 96, 120; Троллейбус №17.; Войти в здание через отдельный вход со стороны Победы б-р. Вход расположен возле наружной лестницы, ведущей на 2 этаж.(1 эт.)",
                                    "work_schedule": "пн-пт:10:00-19:30; сб:10:00-14:00; вс:NODAY",
                                    "shipping_days": 2,
                                    "cod": true,
                                    "card": true,
                                    "gps_location": {
                                        "latitude": "51.711665",
                                        "longitude": "39.163429"
                                    },
                                    "kladr_id": "36000001000",
                                    "shipping_methods": [
                                        11,
                                        44,
                                        79,
                                        136,
                                        91,
                                        21,
                                        175
                                    ],
                                    "limits": {
                                        "max_weight": {
                                            "value": 15,
                                            "unit": "kg"
                                        },
                                        "dimension_sum": 180
                                    },
                                    "prepare_address": {
                                        "administrative_area": "Воронежская обл",
                                        "settlement": "г Воронеж",
                                        "street": "ул 60 Армии",
                                        "house": "27",
                                        "postal_code": "394077"
                                    }
                                },
                                "cashless_payment": false
                              }
                     }';
         }

         $tmp .= '           ],
                    "id": "JsonRpcClient.js"
                }';
        $tmp = json_decode($tmp, true);
        return $tmp;

    }

}

?>
