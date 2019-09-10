<?php

namespace shiptor;

class batches extends \abstracts\batches {

    public function getOrderList($dateFrom, $dateTo) { //Отбор списка заказов за временной интервал

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "getPackages";


        $query['params']['archived'] = false;
        $query['params']['delivered'] = true;

        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);

        if(!$request) {

            $answer['error']['code'] = "_1";
            $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

            return $answer;

        }
        if(!is_array($request)) {

            $answer['error']['code'] = "_2";
            $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
            $answer['error']['info'][] = print_r($request, true);

            return $answer;

        }

        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];


        foreach($request as $orderId => $order) if(strtotime($order['created_at']) >= strtotime($dateFrom) && strtotime($order['created_at']) <= strtotime($dateTo." 23:59")) {

            $response['Invoices'][$orderId]['CustomerNumber'] = $order['external_id'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Code'] = $order['status'];

            /*foreach($order['history'] as $historyId => $history) {

                $response['Invoices'][$orderId]['Encloses']['Statuses'][$historyId]['Description'] = $history['event'].' '.$history['description'];
                $response['Invoices'][$orderId]['Encloses']['Statuses'][$historyId]['Modified'] =  date("d.m.Y H:i:s", strtotime($history['date']));

            }*/

            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Description'] = $order['history'][0]['event'].' '.$order['history'][0]['description'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Modified'] =  date("d.m.Y H:i:s", strtotime($order['history'][0]['date']));

            $response['Invoices'][$orderId]['Number'] = $order['id'];

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
    public function getLabel($putdata='', $dateFrom='', $dateTo='') { //Печать Наклеек

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        foreach($orders as $orderId => $order) {

            $query['id'] = "JsonRpcClient.js";
            $query['jsonrpc'] = "2.0";
            $query['method'] = "getPackage";

            $query['params']['id'] = $order;

            $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
            if($request['error']) return ['error' => $request['error']];
            $request = $request['result'];
            if($request['error']) return ['error' => $request['error']];

            if($request['label_url']) $response .= '<img style="height: 330px; width: 359px;" src="'.$request['label_url'].'" />'; else $errorInfo = 'Ссылка на этикетку не передана.';

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        require_once 'utils/mpdf/vendor/autoload.php';

        $mpdf = new \mPDF();
        $mpdf->WriteHTML($response);
        $mpdf->Output();

        die();

        //return $response;

        /*
        Shipping - getPackage — Получение статуса посылки
        POST
        https://api.shiptor.ru/shipping/v1

        Разрешено: По токену

        Параметр
        Название	                                    Тип	                        Описание
        id	                                            String
        По умолчанию: JsonRpcClient.js

        jsonrpc	                                        Number                      По умолчанию: 2.0

        method	                                        String                      По умолчанию: getPackage

        params	                                        Object
                        id необ.	                    Number                      Идентификационный номер посылки (обязательно если не задан external_id)

                        external_id необ.	            String                      Идентификационный номер посылки в магазине (обязательно если не задан id)

        Пример запроса:

            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "getPackage",
                "params": {
                    "id": 600
                }
            }

        Результат успешного выполнения:
        HTTP/1.1 200 OK

            {
              "jsonrpc": "2.0",
              "result": {
                  "id": 11578,
                  "external_id": "ASD123",
                  "status": "new",
                  "tracking_number": "RP11830",
                  "external_tracking_number": "TRACK1234",
                  "created_at": "2016-10-25 00:11:25",
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
                      "receiver": "Имя Фамилия Отчество",
                      "name": "Имя",
                      "surname": "Фамилия",
                      "patronymic": "Отчество",
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
                  "shipment": {
                    "id": 29,
                    "pickup_date": "2016-12-29",
                    "pickup_time": "с 09:00 до 18:00",
                    "confirmed": false,
                    "courier": {
                      "slug": "dpd",
                      "name": "DPD"
                    },
                    "address": {
                      "receiver": "Иванов Сергей Петрович",
                      "name": "Сергей",
                      "surname": "Иванов",
                      "patronymic": "Петрович",
                      "email": "mail@example.com",
                      "phone": "88002000600",
                      "country_code": null,
                      "administrative_area": null,
                      "settlement": null,
                      "address_line_1": "60 Армии ул., д. 27",
                      "postal_code": null,
                      "street": null,
                      "house": null,
                      "apartment": null,
                      "kladr_id": "36000001000"
                    }
                },
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
                    "event": "Создана",
                    "description": ""
                  },
                ],
                "orders": [
                  {
                    "transaction": 9887,
                    "lines": [
                      {
                        "sum": "136.68",
                        "service": "cash-on-delivery",
                        "package": 11578,
                        "pickup": null
                      }
                    ]
                  }
                ],
                "problems": [
                    {
                        "type": "violated-constraints-delivery-method",
                        "name": "Нарушены ограничения метода доставки",
                        "description": "Невозможно доставить по указанному адресу.",
                        "created_at": "2018-03-30 14:36:36",
                        "resolved_at": "2018-03-30 15:11:17"
                    },
                ],
                ...
              },
              "id": "JsonRpcClient.js"
            }
        */

    }
    public function create($orders='', $dateFrom='', $dateTo='') { //Создаёт партию

        return $response['error'] = 'Служба shiptor не поддерживает партии!';

    }
    public function getInfo($invoiceNumber='', $reestrNumber='') { //Запрашивает данные об партиях

        return $response['error'] = 'Служба shiptor не поддерживает партии!';

    }
    public function removeOrder($orderNumber='', $invoiceNumber='') { //Исключение заказа на доставку из всех партий

        return $response['error'] = 'Служба shiptor не поддерживает партии!';

    }

}
