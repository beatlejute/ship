<?php

namespace shiptor;

class allocates extends \abstracts\allocates {

    public function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo='') { //Вызов курьера для отгрузки

        $batches = new batches($this->authorization);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;

        foreach($request['Invoices'] as $orderId => $order) {

            $orders[] = $order['Number'];

        }


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "getWarehouses";

        $Address = ($City ? $City.', ' : '').$Address;


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];

        foreach($request as $warehouseId => $warehouse) if($warehouse['address'] == $Address) $stock = $warehouse['id'];


        /*
        Shipping - getWarehouses — Получение списка складов
        POST
        https://api.shiptor.ru/shipping/v1
        Разрешено: По токену

        Параметр
        Название	                                Тип	                Описание
        id	                                        String              По умолчанию: JsonRpcClient.js

        jsonrpc	                                    Number	            По умолчанию: 2.0

        method	                                    String	            По умолчанию: getWarehouses

        params	                                    Object

        Пример запроса:
            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "getWarehouses",
                "params": {}
            }

        Результат успешного выполнения:
        HTTP/1.1 200 OK

            {
              "jsonrpc": "2.0",
              "result": [
                {
                  "id": 1553,
                  "name": "mail@mail.ru",
                  "address": "394000, Россия, Московская обл, г Москва, ул Димитрова, 3, 37",
                  "phone": "+7 900 305-45-45",
                  "comment": ""
                },
                [...]
              ]
              "id": "JsonRpcClient.js"
            }
        */

        if(!$stock) {

            $query['id'] = "JsonRpcClient.js";
            $query['jsonrpc'] = "2.0";
            $query['method'] = "addWarehouse";

            $query['params']['name'] = $Address;
            $query['params']['address'] = $Address;
            $query['params']['phone'] = $Phone;
            $query['params']['comment'] = 'Контакт: '.$FIO;


            $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
            if($request['error']) return ['error' => $request['error']];
            $request = $request['result'];
            if($request['error']) return ['error' => $request['error']];

            $stock = $request['id'];

            /*
             Shipping - addWarehouse — Добавление нового склада
            POST
            https://api.shiptor.ru/shipping/v1
            Разрешено: По токену

            Параметр
            Название	                                        Тип	                            Описание
            id	                                                String                          По умолчанию: JsonRpcClient.js

            jsonrpc	                                            Number                          По умолчанию: 2.0

            method	                                            String                          По умолчанию: addWarehouse

            params	                                            Object
              name	                                            String                          Название склада

              address	                                        String                          Адрес склада

              phone	                                            String                          Контактный телефон

              comment необязательный	                        String                          Комментарий


            Пример запроса:

                {
                    "id": "JsonRpcClient.js",
                    "jsonrpc": "2.0",
                    "method": "addWarehouse",
                    "params": {
                        "name": "mail@mail.ru",
                        "address": "394000, Россия, Московская обл, г Москва, ул Димитрова, 3, 37",
                        "phone": "+7 900 305-45-45",
                        "comment": ""
                    }
                }


            Результат успешного выполнения:
            HTTP/1.1 200 OK

                {
                  "jsonrpc": "2.0",
                  "result": {
                    "id": 1553,
                    "name": "mail@mail.ru",
                    "address": "394000, Россия, Московская обл, г Москва, ул Димитрова, 3, 37",
                    "phone": "+7 900 305-45-45",
                    "comment": ""
                  },
                  "id": "JsonRpcClient.js"
                }
             */

        }


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "addPickUp";

        $query['params']['warehouse_id'] = $stock;
        $query['params']['date'] = date("Y-m-d", strtotime($Date));
        $query['params']['packages'] = $orders;
        $query['params']['comment'] = ($TimeStart ? 'Время: с '.$TimeStart.' по '.$TimeEnd.' ' : '').'Контакт: '.$FIO.' '.$Phone;



        /*
        "id": 27,
        "status": "waiting-process",
        "packages": [618],
        "date": "2016-06-10",
        "warehouse": {
          "id": 1,
          "name": "Главный склад",
          "address": "119021, Россия, Московская обл, г Москва, проспект Комсомольский, 3",
          "phone": "+7 910 123-45-67",
          "comment": "Въезд со двора"
        },
        "comment": "Оформление забора груза из 50 посылок"
        */

        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];

        /*
        {
          "CourierRequestRegistred": "<Признак успешности регистрации вызова курьера (true/false)>",
          "OrderNumber": "<Номер отгрузки>",
          "ErrorMessage": "<Описание ошибки>"
        }
        */


        $answer["CourierRequestRegistred"] = true;
        $answer["OrderNumber"] = $request['id'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

        /*
        Shipping - addPickUp — Оформление забора груза со склада

        POST
        https://api.shiptor.ru/shipping/v1

        Разрешено: По токену

        Параметр
        Название	                                    Тип	                        Описание
        id	                                            String	                    По умолчанию: JsonRpcClient.js

        jsonrpc	                                        Number	                    По умолчанию: 2.0

        method	                                        String	                    По умолчанию: addPickUp

        params	                                        Object
                        warehouse_id	                Number                      Номер склада (см. метод getWarehouses)

                        date	                        String                      Дата забора в формате Y-m-d

                        packages	                    Array                       Массив номеров пакетов

                        comment необязательный	        String                      Комментарий к забору

        Пример запроса:

            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "addPickUp",
                "params": {
                    "warehouse_id": 1,
                    "date": "2016-06-10",
                    "packages": [
                        615
                    ],
                    "comment": "Оформление забора груза из 50 посылок"
                }
            }


        Результат успешного выполнения:
        HTTP/1.1 200 OK

            {
              "jsonrpc": "2.0",
              "result": {
                "id": 27,
                "status": "waiting-process",
                "packages": [618],
                "date": "2016-06-10",
                "warehouse": {
                  "id": 1,
                  "name": "Главный склад",
                  "address": "119021, Россия, Московская обл, г Москва, проспект Комсомольский, 3",
                  "phone": "+7 910 123-45-67",
                  "comment": "Въезд со двора"
                },
                "comment": "Оформление забора груза из 50 посылок"
              },
              "id": "JsonRpcClient.js"
            }

        -32602
        Название	                            Тип	                        Описание
        InvalidWarehouse	                    Object                      Warehouse was not found.

        InvalidDate	                            Object                      Invalid date. YYYY-MM-DD format is required.

        InvalidDateRange	                    Object                      Invalid date. Date must be in range %s and %s.

        InvalidPackages	                        Object                      Invalid packages. Packages list must not be empty and only new packages without pick up are allowed.

        InvalidCommentShort	                    Object                      Comment is too short. Minimum length is %s.

        InvalidCommentLong	                    Object                      Comment is too long. Maximum length is %s.

        */

    }
    public function cancel($allocateNumber) { //Отмена отгрузки

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "cancelPickUp";

        $query['params']['id'] = $allocateNumber;


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];

        /*
        {
          "OrderNumber": "<Номер заказа>",
          "Canceled": "<Результат запроса (true/false)>"
        }
         */

        $answer["Canceled"] = ($request['status'] == 'canceled');
        $answer["OrderNumber"] = $request['id'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

        /*
        Shipping - cancelPickUp — Отмена забора груза
        POST
        https://api.shiptor.ru/shipping/v1
        Разрешено: По токену

        Параметр
        Название	                                            Тип	                            Описание
        id	                                                    String	                        По умолчанию: JsonRpcClient.js

        jsonrpc	                                                Number	                        По умолчанию: 2.0

        method	                                                String	                        По умолчанию: cancelPickUp

        params	                                                Object
          id	                                                Number

        Пример запроса:
            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "cancelPickUp",
                "params": {
                    "id": 27
                }
            }

        Результат успешного выполнения:
        HTTP/1.1 200 OK

            {
              "jsonrpc": "2.0",
              "result": {
                "id": 27,
                "status": "canceled",
                "packages": [
                  618
                ]
                "date": "2016-06-10",
                "warehouse": {
                  "id": 1,
                  "name": "Главный склад",
                  "address": "119021, Россия, Московская обл, г Москва, проспект Комсомольский, 3",
                  "phone": "+7 910 123-45-67",
                  "comment": "Въезд со двора"
                },
                "comment": "Оформление забора груза из 50 посылок"
              },
              "id": "JsonRpcClient.js"
            }
         */

    }

}
