<?php

namespace shiptor;

class order extends \abstracts\order {

    public function getCost($fromCity, $fromRegion='', $toCity='', $toRegion='', $toPoint='', $index='', $length='', $depth='', $width='',$weight=1, $count=1, $declaredValue=0, $invoiceNumber='') { //Расчёт стоимости доставки

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "calculateShipping";


        if($fromId != '77000000000') $query['params']['kladr_id_from'] = $fromId;
        $query['params']['kladr_id'] = $toId;


        $query['params']["weight"] = $weight;
        $query['params']["length"] = $length;
        $query['params']["width"] = $width;
        $query['params']["height"] = $depth;

        if($declaredValue) $query['params']['declared_cost'] = $declaredValue;

        $request = $this->authorization->query(json_encode($query), "public/v1", "POST");
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result']["methods"];
        if($request['error']) return ['error' => $request['error']];

        /*if(sizeof($request)) {

            $tariffList = $info->tariffList();

        }*/

        foreach($request as $tariffId => $tariff) if($tariff['status']=='ok') {

            $tariffs[$tariffId]['name'] = $tariff['method']['name'];
            switch ($tariff['method']['category']) {
                case 'delivery-point':
                    $tariffs[$tariffId]['mode'] = 'С-С';
                    break;
                case 'delivery-point-to-delivery-point':
                    $tariffs[$tariffId]['mode'] = 'С-С';
                    break;
                case 'to-door':
                    $tariffs[$tariffId]['mode'] = 'С-Д';
                    break;
                case 'delivery-point-to-door':
                    $tariffs[$tariffId]['mode'] = 'С-Д';
                    break;
                case 'door-to-door':
                    $tariffs[$tariffId]['mode'] = 'Д-Д';
                    break;
                case 'door-to-delivery-point':
                    $tariffs[$tariffId]['mode'] = 'Д-С';
                    break;
                case 'post-office':
                    $tariffs[$tariffId]['mode'] = 'С-Д';
                    break;
            }
            $tariffs[$tariffId]['id'] = $tariff['method']['id'];
            $tariffs[$tariffId]['DPMin'] = intval($tariff['days']);
            $tariffs[$tariffId]['DPMax'] = intval($tariff['days'])+1;
            $tariffs[$tariffId]['cost'] = $tariff['cost']['total']['sum'];
            $tariffs[$tariffId]['info'][] = $tariff['method']['courier'];
            $tariffs[$tariffId]['info'][] = $tariff['method']['description'];

        }

        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffs;

        /*
            Public - calculateShipping — Расчет стоимости доставки

            POST
            https://api.shiptor.ru/public/v1

            Разрешено: Всем

            Параметр
                Название	                Тип	            Описание
                id	                        String          По умолчанию: JsonRpcClient.js

                jsonrpc	                    Number          По умолчанию: 2.0

                method	                    String          По умолчанию: calculateShipping

                params	                    Object
                    length	                Number          Длина, см

                    width	                Number          Ширина, см

                    height	                Number          Высота, см

                    weight	                Number          Вес, кг

                    cod	                    Number          Сумма наложенного платежа, руб. Если отсутствует - передавайте 0

                    declared_cost	        Number          Объявленная ценность, руб. Минимально допустимое значение - 10. Если есть наложенный платеж, то он должен быть равен объявленной ценности.

                    country_code необ.	    String          Код страны для расчета (по умолчанию "RU")

                                                            Допустимые значения: "RU", "KZ", "BY"

                    courier необ.	        String          Строковый идентификатор курьерской службы в системе

                                                            Допустимые значения: "shiptor", "boxberry", "cdek", "dpd", "bpost", "pickpoint", "russian-post", "shiptor-one-day", "shiptor-oversize"

                    kladr_id_from необ. 	String          Идентификатор КЛАДР населенного пункта отправителя

                    kladr_id	            String          Идентификатор КЛАДР населенного пункта получателя


            Пример запроса:
                {
                    "id": "JsonRpcClient.js",
                    "jsonrpc": "2.0",
                    "method": "calculateShipping",
                    "params": {
                        "length": 10,
                        "width": 10,
                        "height": 10,
                        "weight": 10,
                        "cod": 10,
                        "declared_cost": 10,
                        "kladr_id": "01000001000",
                        "courier": "dpd"
                    }
                }

            Результат успешного выполнения:
                HTTP/1.1 200 OK
                {
                  "jsonrpc": "2.0",
                  "result": [
                  {
                      "request": {
                          "length": 10,
                          "width": 10,
                          "height": 10,
                          "weight": 10,
                          "cod": 10,
                          "declared_cost": 10,
                          "kladr_id": "01000001000",
                          "courier" : "dpd"
                      },
                      "methods": [
                          {
                              "status": "ok",
                              "method": {
                                  "id": 16,
                                  "name": "DPD Курьер (Авиа)",
                                  "category": "to-door",
                                  "courier": "dpd"
                              },
                              "cost": {
                                  "services": [
                                      {
                                          "service": "shipping",
                                          "sum": 1858.5,
                                          "currency": "RUB",
                                          "readable": "1 858,50 ₽"
                                      }
                                  ],
                                  "total" {
                                      "sum": 581.75,
                                      "currency": "RUB",
                                      "readable": "581,75 руб."
                                  }
                              },
                              "days": "3 дня"
                          }
                      ]
                  },
                  "id": "JsonRpcClient.js"
                }

            -32602
            Название	                            Тип	            Описание
            InvalidDimension	                    Object          Must be a numeric greater than 0 or null.

            InvalidDeclaredCost	                    Object          Must be a numeric greater or equal 0.

            InvalidKladrId	                        Object          Must contain scalar value.

            InvalidCountryCode	                    Object          Invalid country code. Allowed codes: ...

            InvalidCourierName	                    Object          Invalid courier name. Allowed: ...

            InvalidPickUpType	                    Object          Pick up type invalid.
        */

    }
    public function create($putdata) { //Создание заказа на доставку

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "addPackages";
        //$query['params']['no_gather'] = true;

        if(["Pickup"]["selfPickup"]) $query['params']['shipment']['type'] = 'delivery-point';
        else  $query['params']['shipment']['type'] = 'standard';

        foreach($orders as $orderId => $order) {

            $query['params']['packages'][$orderId]["external_id"] = $order["orderNumber"];
            $query['params']['packages'][$orderId]["declared_cost"] = $order["declaredValue"];
            if($order["payOnDelivery"]) $query['params']['packages'][$orderId]["cod"] = $order["declaredValue"];

            $query['params']['packages'][$orderId]['declared_cost'] = $order["declaredValue"];
            $query['params']['packages'][$orderId]['departure']['shipping_method'] = intval($order["tariff"]);
            $query['params']['packages'][$orderId]['departure']["cashless_payment"] = ($order["payment"] == 'card');

            $query['params']['packages'][$orderId]['departure']['comment'] = $order["Address"]["description"].$order["Address"]["Info"];

            $country = $info->getCountryInfo($order["Address"]["country"]);
            $query['params']['packages'][$orderId]['departure']['address']['country'] = $country['code'];

            if($order["Address"]["index"]) $query['params']['packages'][$orderId]['departure']['address']['postal_code'] = $order["Address"]["index"];
            if($order["Address"]["region"]) $query['params']['packages'][$orderId]['departure']['address']['administrative_area'] = $order["Address"]["region"];
            if($order["Address"]["city"]) $query['params']['packages'][$orderId]['departure']['address']['settlement'] = $order["Address"]["city"];
            if($order["Address"]["street"])  $query['params']['packages'][$orderId]['departure']['address']['street'] = $order["Address"]["street"];
            if($order["Address"]["building"]) $query['params']['packages'][$orderId]['departure']['address']['house'] = $order["Address"]["building"].($order["Address"]["Housing"] ? '/'.$order["Address"]["Housing"] : '');
            if($order["Address"]["Apartment"]) $query['params']['packages'][$orderId]['departure']['address']['apartment'] = $order["Address"]["Apartment"];

            $query['params']['packages'][$orderId]['departure']['address']['receiver'] = $order["recipient"]["name"];
            $query['params']['packages'][$orderId]['departure']['address']['email'] = $order["recipient"]["email"];
            $query['params']['packages'][$orderId]['departure']['address']['phone'] = $order["recipient"]["phone"];


            if($order["toPoint"]) {

                $query['params']['packages'][$orderId]['departure']["delivery_point"] = intval($order["toPoint"]);

                if(!$query['params']['packages'][$orderId]['departure']['address']['administrative_area'] || !$query['params']['packages'][$orderId]['departure']['address']['settlement']) {

                    $info = new info($this->authorization, $this->memcache);
                    $parcelShop = $info->parcelShopsList(null,null,null,null,null, $order["toPoint"]);
                    $query['params']['packages'][$orderId]['departure']['address']['administrative_area'] = $parcelShop[0]['Region'];
                    $query['params']['packages'][$orderId]['departure']['address']['settlement'] = $parcelShop[0]['CitiName'];

                }
            }

            foreach($order["Places"] as $placeId => $place) {

                /*$query[$orderId]["weight"] = $place["Weight"];
                $query[$orderId]["length"] = $place["Depth"];
                $query[$orderId]["width"] = $place["Width"];
                $query[$orderId]["height"] = $place["Height"];*/

                foreach($place["SubEncloses"] as $goodId => $good) {

                    $query['params']['packages'][$orderId]["products"][] = [
                        "shopArticle" => $good["GoodsCode"],
                        "count" => $good["count"] ?: 1,
                        "price" => $good["Price"]
                    ];

                }


            }

        }

        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result']["packages"];
        if($request['error']) return ['error' => $request['error']];

        foreach($request as $orderId => $order) {


            $response['CreatedSendings'][$orderId]['id'] = $order['id'];
            $response['CreatedSendings'][$orderId]['InvoiceNumber'] = $order['id'];
            $response['CreatedSendings'][$orderId]['orderNumber'] = $order["external_id"];

                /*
                $response['RejectedSendings'][$orderId]['orderNumber'] = $query[$queryId]["id"];
                $response['RejectedSendings'][$orderId]['ErrorCode'] = $response[$queryId]->errors[0]->code;
                $response['RejectedSendings'][$orderId]['ErrorMessage'] = $response[$queryId]->errors[0]->descr ? $response[$queryId]->errors[0]->descr : $response[$queryId]->errors[0];
                */

        }

        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
         Shipping - addPackages (standard) — Добавить несколько пакетов

        POST
        https://api.shiptor.ru/shipping/v1
        Разрешено: По токену

        Параметр

            Название	                    Тип	                Описание
            id	                            String              По умолчанию: JsonRpcClient.js

            jsonrpc	                        Number              По умолчанию: 2.0

            method	                        String              По умолчанию: addPackages

            params	                        Object
                        shipment	        Object              Параметры доставки

                        type	            String              Стандартый тип добавления

                                                                Допустимые значения: "standard"

                        packages	        Array               Массив пакетов (описание структуры пакета см. метод AddPackage)


        Пример запроса:
            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "addPackages",
                "params": {
                    "shipment": {
                        "type": "standard"
                    },
                    "packages": [
                        {
                            "length": 10,
                            "width": 10,
                            "height": 10,
                            "weight": 10,
                            "cod": 1000,
                            "declared_cost": 1000,
                            "external_id": "A1234",
                            "photos": [
                                "/9j/4AAQSkZJRgABAQAAAQABAA....",
                                "/9j/g1BNd28JImnijI7M4HQAAZRJABAA...."
                            ],
                            "departure": {
                                "shipping_method": 11,
                                "delivery_point": 4603,
                                "delivery_time": 1,
                                "address": {
                                    "country": "RU",
                                    "receiver": "Иванов Сергей Петрович",
                                    "email": "mail@example.com",
                                    "phone": "+78002000600",
                                    "postal_code": "394063",
                                    "administrative_area": "Воронежская область",
                                    "settlement": "Воронеж",
                                    "street": "60 Aрмии",
                                    "house": "27",
                                    "apartment": "5",
                                    "kladr_id": "36000001000"
                                }
                            }
                        }
                    ]
                }
            }

        Результат успешного выполнения:
            HTTP/1.1 200 OK
            {
              "jsonrpc": "2.0",
              "result": {
                  "packages": [
                      {
                          "id": 72370,
                          "external_id": "A1234",
                          "status": "new",
                          "tracking_number": "RP72370",
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
                      }
                  ]
              },
              "id": "JsonRpcClient.js"
            }
         */

    }
    public function edit($orderNumber, $invoiceNumber='', $recipientName='', $recipientPhone='', $recipientEmail='', $declaredValue='', $toPoint='', $addressIndex='', $addressCountry='', $addressRegion='', $addressCity='', $addressStreet='', $addressBuilding='', $addressHousing='', $addressApartment='', $addressPorch='', $addressFloor='', $addressInfo='') { //Изменение заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "editPackage";

        $order = $this->getInfo($orderNumber, $invoiceNumber);

        $query['params']['declared_cost'] = $order['info']['Sum'];
        $query['params']['departure']['shipping_method'] = $order['info']['tariff'];


        $query['params']['id'] = $invoiceNumber;
        $query['params']['departure']['address']['country'] = $addressCountry ?: 'RU';
        $query['params']['departure']['address']['postal_code'] = $addressIndex ?: $order['info']['address']['postal_code'];
        $query['params']['departure']['address']['administrative_area'] = $addressRegion ?: $order['info']['address']['administrative_area'];
        $query['params']['departure']['address']['settlement'] = $addressCity ?: $order['info']['address']['settlement'];
        $query['params']['departure']['address']['street'] = $addressStreet ?: $order['info']['address']['street'];
        $query['params']['departure']['address']['house'] = $addressBuilding.($addressHousing ? '/'.$addressHousing : '') ?: $order['info']['address']['house'];
        $query['params']['departure']['address']['apartment'] = $addressApartment ?: $order['info']['address']['apartment'];

        $query['params']['departure']['address']['receiver'] = $recipientName ?: $order['info']['address']['receiver'];
        $query['params']['departure']['address']['email'] = $recipientEmail ?: $order['info']['address']['email'];
        $query['params']['departure']['address']['phone'] = $recipientPhone ?: $order['info']['address']['$recipientPhone'];


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];

        $response["InvoiceNumber"] = $request['id'];
        $response["GCInvoiceNumber"] = $request['external_id'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Shipping - editPackage — Изменение посылки
        POST
        https://api.shiptor.ru/shipping/v1
        Разрешено: По токену

        Параметр

        Название	                                    Тип	            Описание
        id	                                            String	        По умолчанию: JsonRpcClient.js

        jsonrpc	                                        Number          По умолчанию: 2.0

        method	                                        String          По умолчанию: editPackage

        params	                                        Object
                    id	                                Number          Идентификатор

                    length	                            Number          Длина, см

                    width	                            Number          Ширина, см

                    height	                            Number          Высота, см

                    weight	                            Number          Вес, кг

                    cod	                                Number          Сумма наложенного платежа, руб. Если отсутствует - передавайте 0

                    no_gather необ.	                    Boolean         Не собирать посылку (только фулфилмент)

                                                                        По умолчанию: false

                    declared_cost	                    Number          Объявленная ценность, руб. Минимально допустимое значение - 10. Если есть наложенный платеж, то он должен быть равен объявленной ценности.

                    external_id необ.	                String          Уникальный идентификатор заказа в вашем магазине

                    departure	                        Object          Данные об отправлении

                        shipping_method	                Number          ID способа доставки

                        delivery_point необ.	        String          ID пункта самовывоза

                        delivery_time необ.	            Number          Рекомендованное время доставки

                                                                        Допустимые значения: "смотреть метод getDeliveryTime"

                        cashless_payment необ.	        Boolean         Оплата картой

                        comment необ.	                String          Комментарий

                        address	                        Object          Данные об адресе доставки

                            country	                    String          Страна

                                                                        Допустимые значения: "RU", "KZ", "BY"

                            name	                    String          Имя

                            surname	                    String          Фамилия

                            patronymic необ.	        String          Отчество

                            receiver	                String          Ф.И.О или название организации

                            email	                    String          Адрес электронной почты

                            phone	                    String          Номер телефона в международном формате (+7…)

                            postal_code	                String          Почтовый индекс

                            administrative_area необ.	String          Область. Можно оставить пустой, если передан kladr_id.

                            settlement необ.	        String          Населенный пункт. Можно оставить пустым, если передан kladr_id.

                            street	                    String          Улица

                            house	                    String          Дом

                            apartment необ.	            String          Квартира

                            kladr_id необ.	            String          Код КЛАДР населенного пункта, можно получить из справочника населенных пунктов. При отсутствии будет определен автоматически.

                        products необ.	                Array           Cписок продуктов (Только пользователям которым доступен fulfilment)

                            shopArticle	                String          Артикул в магазине

                            count	                    Number          Количество товаров

                            price	                    Number          Цена продажи, обязательно если товар ранее не существовал

                            vat	                        Number          Ндс, если не задано берё                        тся из настрок аккаунта (допустимо null, 0, 10, 18)

                        photos	                        Array           Массив фотографий посылки, закодированных в base64

                        services	                    Array           Список услуг

                            shopArticle	                String          Артикул в магазине

                            count	                    Number          Количество оказаных услуг

                            price	                    Number          Цена услуги

                            vat	                        Number          Ндс, если не задано берётся из настрок аккаунта (допустимо null, 0, 10, 18)

        Пример запроса:
            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "editPackage",
                "params": {
                    "id": 72400,
                    "length": 10,
                    "width": 10,
                    "height": 10,
                    "weight": 10,
                    "cod": 10,
                    "declared_cost": 10,
                    "external_id": "ASD123",
                    "photos": [
                        "/9j/4AAQSkZJRgABAQAAAQABAA....",
                        "/9j/g1BNd28JImnijI7M4HQAAZRJABAA...."
                    ],
                    "departure": {
                        "shipping_method": 11,
                        "delivery_point": null,
                        "delivery_time": 1,
                        "cashless_payment": true,
                        "comment": "Комментарий",
                        "address": {
                            "country": "RU",
                            "receiver": "Имя Фамилия Отчество",
                            "name": "Имя",
                            "surname": "Фамилия",
                            "patronymic": "Отчество",
                            "email": "test@example.com",
                            "phone": "+78005553535",
                            "postal_code": "101000",
                            "administrative_area": "Московская обл",
                            "settlement": "Москва",
                            "street": "Красная пл.",
                            "house": "1",
                            "apartment": "1",
                            "kladr_id": "01000001000"
                        }
                    },
                    "products": [
                        {
                            "shopArticle": "CSV48",
                            "count": 20
                        },
                        {
                            "shopArticle": "CSV10",
                            "count": 10
                        },
                        {
                            "shopArticle": "CSV1",
                            "count": 1
                        }
                    ]
                }
            }

        Результат успешного выполнения:

        HTTP/1.1 200 OK
            {
              "jsonrpc": "2.0",
              "result": [
                {
                  "id": 11578,
                  "external_id": "ASD123",
                  "created_at": "2016-10-25 00:11:25",
                  "status": "new",
                  "weight": 10,
                  "length": 10,
                  "width": 10,
                  "height": 10,
                  "cod": 10,
                  "declared_cost": 10,
                  "delivery_time": "с 10:00 до 13:00",
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
                  "departure": {
                    "shipping_method": {
                      "id": 11,
                      "name": "PickPoint",
                      "category": "delivery-point",
                      "courier": "pickpoint"
                    },
                    "address": {
                      "receiver": "Иванов Сергей Петрович",
                      "name": null,
                      "surname": null,
                      "patronymic": null,
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
                "products": [
                    {
                      "name": "Товар 48",
                      "shopArticle": "CSV48",
                      "count": 20
                    },
                    {
                      "name": "Товар 10",
                      "shopArticle": "CSV10",
                      "count": 10
                    },
                    {
                      "name": "Товар 1",
                      "shopArticle": "CSV1",
                      "count": 1
                    }
                ],
                "pick_up": null,
                "history": []
              },
              "id": "JsonRpcClient.js"
            }

        -32602

        Название	                                Тип	                Описание
        InvalidCod	                                Object              Cash-on-delivery is not available for individuals.

        InvalidDeclaredCost	                        Object              Must be a numeric greater or equal 0.

        InvalidKladrId	                            Object              Must contain only digits or empty value.

        InvalidAddress	                            Object              Address cannot be empty.

        InvalidCashlessPayment	                    Object              Cashless payment value must be true or false.

        InvalidProduct	                            Object              Invalid products.

        InvalidProductCount	                        Object              Invalid product count.

        InvalidDelayedDeliveryAtDate	            Object              Invalid delayed delivery date, it should be be +1..+7 days from today.

        InvalidDelayedDeliveryAtMethod	            Object              You can not specify delayed delivery date with selected delivery method.

        InvalidDeliveryPointDoesNotMatchMethod	    Object              Delivery point does not match the selected method.

        InvalidDeliveryPointMushBeDefined	        Object              Delivery point must be defined for selected method.

        InvalidLength	                            Object              Value can not be longer than 64 characters.

        InvalidDeliveryPointDoesNotCash	            Object              Delivery point does not accept cash payment

        InvalidVat	                                Object              Invalid vat (can be null, 0, 10, 18).
        */

    }
    public function getInfo($orderNumber='', $invoiceNumber='') { //Отслеживание статуса и информация о доставке

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "getPackage";


        if($invoiceNumber) $query['params']['id'] = $invoiceNumber;
        if($orderNumber) $query['params']['external_id'] = $orderNumber;


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);

        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];

        $response['status']['State'] = $request['status'];

        $statusList = $info->statusList();
        foreach($statusList as $status) if($status['State'] == $request['status']) $response['status']['StateMessage'] = $status['StateText'];

        $response['info']['InvoiceNumber'] = $request['id'];
        $response['info']['SenderInvoiceNumber'] = $request['external_id'];
        $response['info']['Sum'] = $request['declared_cost'];
        //$response['info']['label'] = $request['label_url'];
        $response['info']['CreateDate'] = $request['created_at'];
        $response['info']['FIO'] = $request['departure']['address']['receiver'];
        $response['info']['StorageDate'] = $request['departure']['shipment']['pickup_date'];
        $response['info']['tariff'] = $request['departure']['shipping_method']['id'];
        $response['info']['address'] = $request['departure']['address'];

        /*{
    "status": [
        {
            "State": "<код статуса>",
            "ChangeDT": "<дата изменения статуса>",
            "StateMessage": "<описание статуса>"
        }
    ],
    "info": [
        {
            "InvoiceNumber": "<Номер отправления в системе>",
            "SenderInvoiceNumber": "<Номер отправления магазина>",
            "Sum": "<сумма за отправление>"
            "CreateDate": "<дата создания отправления>",
            "FIO": "<ФИО получателя>",
            "StorageDate": "<Срок хранения, если отправление еще не заложено, то пустое поле>",
            "Prolonged": "<true/false – было или нет продление>",
            "Barcodes": [
                "<штрих-код отправления>"
            ],
            "RefundInfo <Информация по возврату денег>": {
                "RefundDate": "<Дата создания акта возврата денег>",
                "RefundNumber": "<Номер акта возврата>",
                "PaymentNumber": "<Номер платежного поручения>",
                "Sum": "<Сумма перечисления>",
                "AgencyFee": "<Сумма агентского вознаграждения>"
            },
            "ReturnInfo <Информация по возврату товара>": {
                "ReturnDocumentDate": "<Дата создания реестра возврата денег>",
                "ReturnDocumentNumber": "<Номер реестра возврата>",
                "ReturnInvoiceNumber": "<Номер возвратного отправления>",
                "ReturnDeliveryDate": "<Дата доставки возврата>",
                "ReturnFromCity": "<Город отправки возврата>",
                "ReturnAddress": "<Адрес доставки возврата>",
                "SubEncloses <Информация о субвложимых>": [
                    {
                        "Line": "<Номер>",
                        "ProductCode": "<Код продукта>",
                        "GoodsCode": "<Код товара>",
                        "name": "<Наименование>",
                        "Price": "<Стоимость>",
                        "Return date": "<Дата возврата (физ. лицом)>",
                        "Reason": "<Причина>"
                    }
                ]
            },
            "ClientReturnAddress <Адрес клиентского возврата>": {
                "CityName": "<Название города>",
                "RegionName": "<Название региона>",
                "Address": "<Текстовое описание адреса>",
                "FIO": "<ФИО контактного лица>",
                "PostCode": "<Почтовый индекс>",
                "Organisation": "<Наименование организации>",
                "PhoneNumber": "<Контактный телефон>",
                "Comment": "<Комментарий>"
            },
            "UnclaimedReturnAddress <Адрес возврата невостребованного>": {
                "CityName": "<Название города>",
                "RegionName": "<Название региона>",
                "Address": "<Текстовое описание адреса>",
                "FIO": "<ФИО контактного лица>",
                "PostCode": "<Почтовый индекс>",
                "Organisation": "<Наименование организации>",
                "PhoneNumber": "<Контактный телефон>",
                "Comment": "<Комментарий>"
            },
            "ChequeNumber": "<номер чека, если несколько, то перечислены через запятую>",
            "PayType": "<тип оплаты отправления cash/card>"
        }
    ]*/

        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

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
    public function cancel($orderNumber='', $invoiceNumber='') { //Отмена заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "removePackage";


        if($invoiceNumber) $query['params']['id'] = $invoiceNumber;
        if($orderNumber) $query['params']['external_id'] = $orderNumber;


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];
        $request = $request['result'];
        if($request['error']) return ['error' => $request['error']];

        $response['Result'] = true;


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
    Shipping - removePackage — Удаление посылки
       POST
       https://api.shiptor.ru/shipping/v1
       Разрешено: По токену

       Параметр
       Название	                        Тип	                                Описание
       id	                                String                              По умолчанию: JsonRpcClient.js

       jsonrpc	                            Number	                            По умолчанию: 2.0

       method	                            String	                            По умолчанию: removePackage

       params	                            Object
         id необязательный	                Number	                            Идентификационный номер посылки (обязательно если не задан external_id)

         external_id необязательный	    String	                            Идентификационный номер посылки в магазине (обязательно если не задан id)


   Пример запроса:
       {
           "id": "JsonRpcClient.js",
           "jsonrpc": "2.0",
           "method": "removePackage",
           "params": {
               "id": 600
           }
       }


   Результат успешного выполнения:
       HTTP/1.1 200 OK

       {
         "jsonrpc": "2.0",
         "result": [
           {
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
               },
               "pickup_date": "2016-12-29",
               "pickup_time": "с 09:00 до 18:00"
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
               "event": "Создана"
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
           ]
         },
         "id": "JsonRpcClient.js"
       }


       -32602
       Название	                Тип	                            Описание
       InvalidProduct	            Object	                        Package was not found.

       InvalidProductRemoved	    Object                          Package already removed.

       InvalidProductAccess	    Object                          Сan not delete a package in this state.
    */

    }

}
