<?php

namespace shiptor;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\batches\getLabel as getLabelTest;



class shiptorBatchesGetLabelTest extends getLabelTest {

    protected function _before() {
        $this->namespace = 'shiptor';
        $this->ClassName = '\\'.$this->namespace.'\\batches';
    }

    protected function _mackInterface($obj) {

        $tmp = '{
            "jsonrpc": "2.0",
            "result": 
                {
                    "id": ' . $obj[0] . ',
                    "external_id": "",
                    "stock": 1,
                    "status": "new",
                    "tracking_number": "RP744171",
                    "external_tracking_number": null,
                    "created_at": "",
                    "weight": 1,
                    "length": 10,
                    "width": 10,
                    "height": 10,
                    "cod": 1000,
                    "declared_cost": 1000,
                    "no_gather": false,
                    "label_url": "https://shiptor.ru/package/64c158ab99085fed5fc23b357040f48eb3cf881213c8c15fee628ce64c318bbf/label.png",
                    "small_label_url": "https://shiptor.ru/package/64c158ab99085fed5fc23b357040f48eb3cf881213c8c15fee628ce64c318bbf/print-label?size=1",
                    "cost": {
                        "shipping_cost": null,
                        "cod_service_cost": null,
                        "compensation_service_cost": null,
                        "total_cost": 0
                    },
                    "departure": {
                        "shipping_method": {
                            "id": 1,
                            "name": "Boxberry Самовывоз",
                            "category": "delivery-point",
                            "group": "boxberry_delivery_point",
                            "courier": "boxberry",
                            "comment": "",
                            "description": null,
                            "help_url": "https://shiptor.ru/help/common/all-delivery-methods#boxberry-selfpickup"
                        },
                        "address": {
                            "receiver": "",
                            "email": "test@test.test",
                            "phone": "+7 912 000-00-00",
                            "country_code": "RU",
                            "administrative_area": "Свердловская обл",
                            "settlement": "г Екатеринбург",
                            "address_line_1": null,
                            "marked_as_trash_at": null,
                            "name": null,
                            "surname": null,
                            "patronymic": null,
                            "postal_code": "620026",
                            "street": "ул Мамина-Сибиряка",
                            "house": "130",
                            "apartment": null,
                            "kladr_id": "66000001000"
                        },
                        "comment": "",
                        "delivery_point": {
                            "id": 55,
                            "courier": "boxberry",
                            "address": "620026, Екатеринбург г, Мамина-Сибиряка ул, д.130",
                            "phones": [
                                "+7(343)361-73-82"
                            ],
                            "trip_description": "Перекресток Мамина-Сибиряка/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                            "work_schedule": "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                            "shipping_days": 3,
                            "cod": true,
                            "card": true,
                            "gps_location": {
                                "latitude": "56.830944",
                                "longitude": "60.620520"
                            },
                            "kladr_id": "66000001000",
                            "shipping_methods": [
                                102,
                                46,
                                4,
                                14,
                                140,
                                76,
                                108,
                                109,
                                75,
                                49,
                                13,
                                3,
                                101,
                                133,
                                149,
                                173,
                                174
                            ],
                            "limits": {
                                "max_weight": {
                                    "value": 31,
                                    "unit": "kg"
                                },
                                "volume": 0.48
                            },
                            "prepare_address": {
                                "administrative_area": "Свердловская обл",
                                "settlement": "г Екатеринбург",
                                "street": "ул Мамина-Сибиряка",
                                "house": "130",
                                "postal_code": "620026"
                            }
                        },
                        "cashless_payment": true
                    },
                    "products": [
                        {
                            "name": "123",
                            "shopArticle": "123",
                            "englishName": null,
                            "count": 1,
                            "price": 1000,
                            "vat": 0
                        }
                    ],
                    "pick_up": null,
                    "checkpoints": [],
                    "history": [
                        {
                            "date": "2018-11-22T09:50:37+03:00",
                            "event": "Адрес изменен",
                            "description": ""
                        },
                        {
                            "date": "2018-11-22T09:50:35+03:00",
                            "event": "Адрес изменен",
                            "description": ""
                        },
                        {
                            "date": "2018-11-22T09:50:34+03:00",
                            "event": "Посылка создана",
                            "description": ""
                        }
                    ],
                    "orders": [],
                    "type": "standard",
                    "method": "api",
                    "archived_at": null,
                    "photo": [],
                    "services": [],
                    "delivery_time": null,
                    "delayed_delivery_at": null,
                    "shipment": null
                },
            "id": "JsonRpcClient.js"
        }';

        return json_decode($tmp, true);

    }

}

?>
