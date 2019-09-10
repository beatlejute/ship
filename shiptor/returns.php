<?php

namespace shiptor;

class returns extends \abstracts\returns {

    public function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum='') { //Регистрация возврата

        return $response['error'] = 'Служба shiptor не поддерживает регистрацию возвратов!';

    }
    public function getReturnsList($dateFrom, $dateTo) { //Получение списка возвратных отправлений

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "getPackages";

        $query['params']['archived'] = false;
        $query['params']['delivered'] = true;
        $query['params']['status'] = 'returned';


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];


        foreach($request as $orderId => $order) if(strtotime($order['created_at']) >= strtotime($dateFrom) && strtotime($order['created_at']) <= strtotime($dateTo." 23:59")) {

            $response['SendingsInfo'][$orderId]['SenderInvoiceNumber'] = $order['external_id'];
            $response['SendingsInfo'][$orderId]['InvoiceNumber'] = $order['id'];
            //$response['SendingsInfo'][$orderId]['ReturnReason'] = $order->getElementsByTagName('Reason')->item(0)->getAttribute('Description');

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
            Shipping - getPackages — Получение списка посылок
            POST
            https://api.shiptor.ru/shipping/v1
            Разрешено: По токену

            Параметр
            Название	                        Тип	                        Описание
            id	                                String                      По умолчанию: JsonRpcClient.js

            jsonrpc	                            Number                      По умолчанию: 2.0

            method	                            String                      По умолчанию: getPackages

            params	                            Object
                     stock необязательный	    Number                      Идентификатор склада

                     page	                    Number                      Страница

                     per_page	                Number                      Количество на странице (маскимум 100)

                     delivered необязательный	Boolean                     (Устаревший) Не показывать доставленные

                                                                            По умолчанию: false

                     returned необязательный	Boolean                     (Устаревший) Не показывать возвратные

                                                                            По умолчанию: false

                     archived необязательный	Boolean                     null все посылки, true - только заархивированные, false - не заархивированные

                                                                            По умолчанию: null

                     type необязательный	    String                      Тип посылок

                                                                            По умолчанию: null

                                                                            Допустимые значения: "standard", "fulfilment", "through"

                     status необязательный	    String                      Статус посылок см. getPackageStatuses

                                                                            По умолчанию: null

            Пример запроса:

                {
                    "id": "JsonRpcClient.js",
                    "jsonrpc": "2.0",
                    "method": "getPackages",
                    "params": {
                        "stock": 1,
                        "page": 1,
                        "per_page": 10,
                        "archived": true,
                        "delivered": true,
                        "returned": false,
                        "type": "standard",
                        "status": "new"
                    }
                }

            Результат успешного выполнения:
            HTTP/1.1 200 OK

                {
                  "jsonrpc": "2.0",
                  "result": [
                    {
                      "id": 11578,
                      "stock": 1,
                      "external_id": "ASD123",
                      "status": "new",
                      "tracking_number": "RP11830",
                      "external_tracking_number": "TRACK1234",
                      "weight": 10,
                      "length": 10,
                      "width": 10,
                      "height": 10,
                      "cod": 10,
                      "declared_cost": 10,
                      "label_url": "https://shiptor.ru/package/0b330fe292f4f8/label.png",
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
                      "cost": {
                        "shipping_cost": "150.00",
                        "cod_service_cost": 40,
                        "compensation_service_cost": 2,
                        "total_cost": 192
                      },
                      "departure": {
                        "shipping_method": {
                          "id": 11,
                          "name": "PickPoint",
                          "category": "delivery-point",
                          "courier": "pickpoint"
                        },
                        "address": {
                          "receiver": "Иванов Сергей Петрович",
                          "name": "Сергей",
                          "surname": "Иванов",
                          "patronymic": "Петрович",
                          "email": "test@example.com",
                          "phone": "+7 800 555-35-35",
                          "country_code": "RU",
                          "postal_code": "101000",
                          "administrative_area": "респ. Адыгея",
                          "settlement": "г. Майкоп",
                          "street": "Красная пл.",
                          "house": "1",
                          "apartment": "1",
                          "kladr_id": "01000001000"
                        },
                        "delivery_point": null,
                        "cashless_payment": false,
                        "comment": null
                      },
                    "pick_up": null,
                    "archived_at": null,
                    "shipment": null,
                    "checkpoints": [
                      {
                        "date": "2016-10-25T12:10:04+03:00",
                        "message": "Отправлена из сортировочного центра",
                        "details": "Сортировочный пункт (Москва)"
                      },
                    ],
                    "history": [
                      {
                        "date": "2016-08-24T13:54:40+03:00",
                        "event": "Создана"
                      },
                    ],
                    "problems": [
                        {
                            "type": "violated-constraints-delivery-method",
                            "name": "Нарушены ограничения метода доставки",
                            "description": "Невозможно доставить по указанному адресу.",
                            "created_at": "2018-03-30 14:36:36",
                            "resolved_at": "2018-03-30 15:11:17"
                        }
                    ],
                    ...
                  },
                  "id": "JsonRpcClient.js"
                }

            -32602
            Название	                    Тип	                    Описание
            InvalidStock	                Object                  You do not have access to a stock.

            InvalidNumber	                Object                  Must be a numeric greater than 0 or null.

        */

    }
    public function getInfo($orders='', $dateFrom='', $dateTo='') { //Получение информации по возвратным отправлениям

        return $response['error'] = 'Служба shiptor не поддерживает получение информации о возвратах!';

    }

}

?>