<?php

namespace easyway;

class order extends \abstracts\order {

    public function getCost($fromCity, $fromRegion='', $toCity='', $toRegion='', $toPoint='', $index='', $length='', $depth='', $width='',$weight=1, $count=1, $declaredValue=0, $invoiceNumber='') { //Расчёт стоимости доставки

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;

        $query["InvoiceNumber"] = $invoiceNumber;
        $query["FromCity"] = urlencode($fromCity);
        $query["FromRegion"] = $fromRegion;
        $query["toCity"] = ($toRegion) ? urlencode($toCity) : urlencode($toCity);
        $query["toPoint"] = $toPoint;
        $query["Count"] = $count ? $count : 1;
        $query["Length"] = $length;
        $query["Depth"] = $depth;
        $query["Width"] = $width;
        $query["Weight"] = $weight;
        if($query["Length"] && $query["Depth"] && $query["Width"]) $query["volume"] = ($query["Length"]*$query["Depth"]*$query["Width"]) * 0.000001;

        if($toPoint) {

            $point = $info->parcelShopsList('', '', '', '', '', $toPoint);
            $query["toCity"] = urlencode($point[0]['CitiName']);

        }

        //print_r($query);
        //print "getTariff?locationFrom=".$query["FromCity"]."&locationTo=".$query["toCity"]."&weight=".$query["Weight"]."&volume=".$query["volume"];

        $request = $this->authorization->query("{}", "getTariff?locationFrom=".$query["FromCity"]."&locationTo=".$query["toCity"]."&weight=".$query["Weight"]."&volume=".$query["volume"], "GET");

        if(isset($_GET['semen1'])) {
            print_r($request);
            exit;
        }
        if($request['error']) return ['error' => $request['error']];


        if(sizeof($request)) {

            $tariffList = $info->tariffList();
            if(isset($_GET['semen2'])) {
                print_r($tariffList);
                exit;
            }
        }

        if($weight) foreach($request as $tariffId => $tariff) if($tariff['total'] || ($tariff['deliveryType']==5 && $request[4]['total'] && $query["Weight"]<=15)) {

            foreach($tariffList as $tariffListId => $tariffListItem) if($tariffListItem['id'] == $tariff['deliveryType']) {

                $tariffs[$tariffId]['name'] = $tariffListItem['name'];
                $tariffs[$tariffId]['mode'] = $tariffListItem['mode'];

            }
            $tariffs[$tariffId]['id'] = $tariff['deliveryType'];
            if($tariff['estDeliveryTime']['min'] != "-") $tariffs[$tariffId]['DPMin'] = $tariff['estDeliveryTime']['min'];
            if($tariff['estDeliveryTime']['max'] != "-") $tariffs[$tariffId]['DPMax'] = $tariff['estDeliveryTime']['max'];

            if($tariff['name']) $tariffs[$tariffId]['name'] = $tariff['name'];
            if($tariff['mode']) $tariffs[$tariffId]['mode'] = $tariff['mode'];

            $tariffs[$tariffId]['cost'] = $tariff['total'];
            if($tariff['deliveryType']==5 && $query["Weight"]<=15 && $request[4]['total']) $tariffs[$tariffId]['cost'] = $request[4]['total'];

            if($toPoint && ($tariffs[$tariffId]['mode'] == "Д-Д" || $tariffs[$tariffId]['mode'] == "С-Д")) unset($tariffs[$tariffId]);

        }

        $tariffs = array_values($tariffs);

        if($request['isError']) {

            $tariffs['errors'] = $request['errors'];
            $tariffs['url'] = "getTariff?locationFrom=".$query["FromCity"]."&locationTo=".$query["toCity"]."&weight=".$query["Weight"]."&volume=".$query["volume"];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffs;

        /*
        getTariff

        Предварительный расчет доставки

        GET-запрос http://apiurl/getTariff?locationFrom=Москва&locationTo=Подольск&weight=1&volume=0.001

        locationFrom - адрес отправления
        locationTo - адрес получения
        weight - вес в кг.
        volume - объем в м3
        Пример ответа:

        [
        {
        "deliveryType": 1,
        "total": 260,
        "estDeliveryTime": {
         "min": "1",
         "max": "2"
         }
        },
        {
        "deliveryType": 2,
        "total": 130,
        "estDeliveryTime": {
         "min": "1",
         "max": "2"
         }
        },
        {
        "deliveryType": 3,
        "total": 0,
        "estDeliveryTime": {
         "min": "-",
         "max": "-"
         }
        },
        {
        "deliveryType": 4,
        "total": 0,
        "estDeliveryTime": {
         "min": "-",
         "max": "-"
         }
        }
        ]
        deliveryType - тип доставки

        1 Авто до двери
        2 Авто до ПВЗ
        3 Авиа до двери
        4 Авиа до ПВЗ
        */

    }
    public function create($putdata) { $orders = $putdata; //Создание заказа на доставку

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //Многоместное отправление

        foreach($orders as $orderId => $order) {

            $query[$orderId]["id"] = $order["orderNumber"];
            $query[$orderId]["locationFrom"] = $order["Pickup"]["fromCity"];
            $query[$orderId]["locationTo"] = $order["Address"] ?
                ($order["Address"]["index"] ? $order["Address"]["index"].', ' : '').
                ($order["Address"]["region"] ? $order["Address"]["region"].', ' : '').
                ($order["Address"]["city"] ? $order["Address"]["city"].', ' : '').
                ($order["Address"]["street"] ? $order["Address"]["street"].', ' : '').
                ($order["Address"]["building"] ? $order["Address"]["building"] : '').
                ($order["Address"]["Housing"] ? '/'.$order["Address"]["Housing"] : '').
                ($order["Address"]["Apartment"] ? ' кв. '.$order["Address"]["Apartment"] : '').
                ($order["Address"]["Porch"] ? ' Подезд '.$order["Address"]["Porch"] : '').
                ($order["Address"]["Floor"] ? ' '.$order["Address"]["Floor"].' Этаж' : '')
                : '';
            if($order["toPoint"]) $query[$orderId]["pickupPointCode"] = $order["toPoint"];
            $query[$orderId]["cargoCount"] = $order["cargoCount"];
            foreach($order["Places"] as $placeId => $place) {

                $query[$orderId]["weight"] = $place["Weight"];
                $query[$orderId]["length"] = $place["Depth"];
                $query[$orderId]["width"] = $place["Width"];
                $query[$orderId]["height"] = $place["Height"];

                if($place["SubEncloses"][0]["GoodsCode"]) {

                    $query[$orderId]["items"][$placeId]["article"] = $place["SubEncloses"][0]["GoodsCode"];
                    $query[$orderId]["items"][$placeId]["name"] = $place["SubEncloses"][0]["name"];
                    $query[$orderId]["items"][$placeId]["count"] = $place["SubEncloses"][0]["count"] ? $place["SubEncloses"][0]["count"] : 1;
                    $query[$orderId]["items"][$placeId]["price"] = $place["SubEncloses"][0]["Price"];

                }

            }
            $query[$orderId]["assessedCost"] = $order["declaredValue"];
            $query[$orderId]["paymentMethod"] = $order["payOnDelivery"] ? 0 : 2;
            if(!$query[$orderId]["paymentMethod"]) $query[$orderId]["paymentMethod"] = ($order["payment"] == 'cart') ? 0 : 1;
            $query[$orderId]["deliveryType"] = intval($order["tariff"]);
            $query[$orderId]["total"] = ($query[$orderId]["paymentMethod"] == 2) ? 0 : $query[$orderId]["assessedCost"];
            if($order["Pickup"]["selfPickup"]) $query[$orderId]["noPickup"] = $order["Pickup"]["selfPickup"];
            $query[$orderId]["recipient"] = $order["recipient"];
            $query[$orderId]["comment"] = $order["description"];

        }

        foreach($query as $queryId => $queryItem) {

            $response[$queryId] = $this->authorization->query(json_encode($queryItem), "createOrder", "POST");
            if(isset($response['error'])) return ['error' => $response['error']];

            //foreach($response[$queryId] as $orderId => $order) {
            if(!$response[$queryId]['isError'] && $response[$queryId]['data']['id']) {
                $response['CreatedSendings'][$queryId]['InvoiceNumber'] = $response[$queryId]['data']['id'];
                $response['CreatedSendings'][$queryId]['orderNumber'] = $query[$queryId]["id"];
            }

            //}

            foreach($response[$queryId]['errors'] as $orderId => $order) if($response[$queryId]['isError']) {

                    $response['RejectedSendings'][$queryId]['orderNumber'] = $query[$queryId]["id"];
                    if(isset($response[$queryId]['errors'][0]['code'])) {

                        $response['RejectedSendings'][$queryId]['ErrorCode'] = $response[$queryId]['errors'][0]['code'];
                        $response['RejectedSendings'][$queryId]['ErrorMessage'] = $response[$queryId]['errors'][0]['descr'] ? $response[$queryId]['errors'][0]['descr'] : $response[$queryId]['errors'][0];

                    } else $response['RejectedSendings'][$queryId]['ErrorMessage'] = $response[$queryId]['errors'];


            }

            unset($response[$queryId]);

        }

        unset($response["data"]);


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;


        /*
        createOrder

        Cоздание заявки на доставку груза

        POST-запрос к ресурсу http://apiurl/createOrder

        Пример запроса

        {
        "id": "test123457",                                          // Идентификатор заказа в системе клиента
        "locationFrom": "Москва, ул. Складочная, д 1, стр 9",        // Адрес отправления
        "locationTo": "Ростов-на-Дону, ул. М. Горького, 130; кв.4",  // Адрес получения
        "pickupPointCode": "123", // Код ПВЗ, полученный методом getPickupPoints из поля guid
                                  // обязательный, если тип доставки 2 или 4
        "cargoCount": "2",        // Количество грузомест
        "weight": 1.5,            // Вес в килограммах
        "length": 15,             // Длина в сантиметрах
        "width": 30,              // Ширина в сантиметрах
        "height": 20,             // Высота в сантиметрах
        "assessedCost": 999.99,   // Оценочная стоимость
        "paymentMethod": 0,       // Способ оплаты (0 - Безналичная, 1 - Наличными, 2 - предоплата (без наложенного платежа)
        "deliveryType": 1,        // Тип доставки
        "total": 999.99,          // Итого с клиента
        "senderId": "",           // Идентификатор отправителя (в случае нескольких юр. лиц), необязательный
        "noPickup": true,         // true - в случае самостоятельной доставки груза на склад Easy Way, необязательный
        "items": [{               // Товарный состав, необязательный
          "article": "123456",    // Артикул
          "name": "Телефон LG",   // Наименование
          "count": 1,             // Количество
          "price": 800            // Цена
          },{
          "article": "123457",
          "name": "Телефон LG",
          "count": 2,
          "price": 199.99
          }
        ],
        "recipient": {           // Получатель
          "name": "Иванов Иван", // ФИО получателя
          "phone": "9055089783"  // Телефон
          },
        "services": [            // Дополнительные услуги, необязательный
          "hardPack",                  // Жесткая упаковка
          "addPack1",                  // Дополнительная упаковка
          "addPack2",                  // Пузырьковая пленка
          "docReturn",                 // Возврат документов
          "loadUnload",                // Погрузочно-разгрузочные работы
          "recipientPayer",            // Оплата получателем
          "nightDelivery"              // Ночная доставка
          ]
        }
        Пример ответа

        {
        "isError": false,
        "errors": [["code": 200, "descr": "Описание"], [...]],
        "data": {
         "id": "000032888"
         }
        }
        */
    }
    public function edit($orderNumber, $invoiceNumber='', $recipientName='', $recipientPhone='', $recipientEmail='', $declaredValue='', $toPoint='', $addressIndex='', $addressCountry='', $addressRegion='', $addressCity='', $addressStreet='', $addressBuilding='', $addressHousing='', $addressApartment='', $addressPorch='', $addressFloor='', $addressInfo='') { //Изменение заказа

        return $response['error'] = 'Служба EasyWay не поддерживает редактирование отправлений!';

    }
    public function getInfo($orderNumber='', $invoiceNumber='') { //Отслеживание статуса и информация о доставке

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //getStatus Запрос статусов заявок

        if($invoiceNumber) $query["number"] = $invoiceNumber;
        else $query["number"] = $orderNumber;

        //print json_encode($query);

        $requeststatus = $this->authorization->query("{}", "getStatus?number=".$query["number"]."&json=1", "GET");
        if($requeststatus['error']) return ['error' => $requeststatus['error']];

        $response['status']['State'] = $requeststatus[0]['statusCode'];
        $response['status']['ChangeDT'] = $requeststatus[0]['date'];
        $response['status']['StateMessage'] = $requeststatus[0]['status'];

        /*
        getStatus

        Запрос статусов заявок

        GET-запрос http://apiurl/getStatus?number=1091840-YD1854000,66012-YD1854327

        number - номера заявок, разделенные запятой
        Пример ответа:

        [
        {
        "orderNumber": "1091840-YD1854000",
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
        "id": "000038700",
        "statusCode": "675f4358-6f61-11e6-80ea-003048baa05f"
        },
        {
        "orderNumber": "66012-YD1854327",
        "date": "2017-05-22T13:32:37",
        "status": "Выдан",
        "arrivalPlanDateTime": "2017-05-20T15:00:00",
        "dateOrder": "2017-05-15T15:27:17",
        "sender": "Москва",
        "receiver": "Брянск",
        "carrierTrackNumber": "000029779",
        "address": "Терминал, Склад ПЭК, г. Ярославль, проспект Октября, 93",
        "deliveryType": "Терминал",
        "phone": "",
        "id": "000038700",
        "statusCode": "b3e0596a-6b97-11e6-80e9-003048baa05f"
        }
        ]

        */


        //getOrderInfo Получение подробной информации по заявкам

        $requestinfo = $this->authorization->query("{}", "getOrderInfo?number=".$query["number"], "GET");

        $response['info']['InvoiceNumber'] = $requestinfo[0]['id'];
        $response['info']['SenderInvoiceNumber'] = $requestinfo[0]['clientId'];
        $response['info']['Sum'] = $requestinfo[0]['total'];
        $response['info']['CreateDate'] = $requestinfo[0]['date'];
        $response['info']['FIO'] = $requestinfo[0]['recipient'];
        $response['info']['StorageDate'] = '';
        $response['info']['Prolonged'] = false;
        $response['info']['PayType'] = $requestinfo[0]['recipient'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*

        getOrderInfo

        Получение подробной информации по заявкам

        GET-запрос http://apiurl/getOrderInfo?number=1091840-YD1854000,66012-YD1854327

        number - номера заявок, разделенные запятой
        Пример ответа:

        [
        {
        "id": "66012-YD1854327",
        "date": "2017-05-15T15:27:17",
        "regionFrom": "Москва",
        "regionTo": "Брянск",
        "addressFrom": "Россия, 0, г Москва, Огородный проезд, 20, К3",
        "addressTo": "ПВЗ, Склад ПЭК, Брянск 241014, ул. Марии Расковой, д. 25",
        "weight": 10.4,
        "volume": 0.0495,
        "length": 37.4,
        "width": 37.6,
        "height": 35.2,
        "accessedCost": 11004,
        "cargoCost": 11004,
        "recipient": "ООО Велес ООО Велес ",
        "recipientPhone": "74832686281",
        "total": 0,            // Наложенный платеж
        "deliveryCost": 334,   // Стоимость доставки
        "outsideZoneCost": 0,  // Стоимость доставки за пределами городской зоны (Оплата километража)
        "insuranceCost": 0,    // Страхование груза
        "addServicesCost": 0   // Дополнительные услуги (Все остальное кроме НП)
        },
        {
        "id": "1091840-YD1854000",
        "date": "2017-05-15T15:08:00",
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
        "recipient": "Иван Иванов Иванович",
        "recipientPhone": "79053049601",
        "total": 6177,
        "deliveryCost": 398,
        "outsideZoneCost": 0,
        "insuranceCost": 0,
        "addServicesCost": 0
        }
        ]

        */

    }
    public function cancel($orderNumber='', $invoiceNumber='') { //Отмена заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if($invoiceNumber) $query[] = $invoiceNumber;
        else {

            $status = $this->authorization->query("{}", "getStatus?number=".$orderNumber."&json=1", "GET");
            if($status['error']) return ['error' => $status['error']];
            $query[] = $status[0]['id'];

        }

        $request = $this->authorization->query(json_encode($query), "cancelOrder", "POST");
        if($request['error']) return ['error' => $request['error']];

        $response['Result'] = $request['data'][0]['cancel'];

        if($request['isError']) {

            $response['ErrorCode'] = $request['errors']['code'];
            $response['Error'] = $request['errors']['descr'];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        cancelOrder

        Отмена заявок на доставку груза

        POST-запрос к ресурсу http://apiurl/cancelOrder

        Пример запроса

        //Массив id заявок, полученных методом createOrder
        [
        "000034152",
        "000034150"
        ]
        Пример ответа

        {
        "isError": false,
        "errors": [["code": 200, "descr": "Описание"], [...]],
        "data": [
         {
         "id": "000034152",
         "cancel": true,
         "descr": "Отменена"
         },
         {
         "id": "000034150",
         "cancel": false,
         "descr": "Не найдена"
         }
        ]
        }
        */

    }

}
