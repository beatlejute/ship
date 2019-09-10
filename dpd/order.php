<?php

namespace dpd;

class order extends \abstracts\order {

    public function getCost($fromCity, $fromRegion='', $toCity='', $toRegion='', $toPoint='', $index='', $length='', $depth='', $width='',$weight=1, $count=1, $declaredValue=0, $invoiceNumber='') { //Расчёт стоимости доставки

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if($index) $query['delivery']['index'] = $index;
        $query['delivery']['regionCode'] = $to[0]["RegionId"];
        $query['delivery']['cityId'] = $toId;
        $query['delivery']['cityName'] = $toCity; //Название города
        $query['delivery']['countryCode'] = 'RU';

        //Способ доставки
        $query['selfDelivery'] = false; //если отправляем до дома то ставим значение false

        //город отправления
        $query['pickup']['cityId'] = $fromId;
        //$query['pickup']['index'] = 111402;
        $query['pickup']['cityName'] = $fromCity;
        $query['pickup']['regionCode'] = $from[0]["RegionId"];
        $query['pickup']['regionCode'] = 'RU';

        // что делать с терминалом
        $query['selfPickup'] = false; // Доставка ОТ терминала // если вы сами довозите до терминала то true если вы отдаёте от двери то false

        $query['weight'] = $weight; //Вес отправки, кг
        if($length && $depth && $width) $query["volume"] = ($length*$depth*$width) * 0.000001;
        $query['declaredValue'] = $declaredValue; //Объявленная ценность (итоговая)


        $request = $this->authorization->query($query, "calculator2?wsdl", "getServiceCost2", 'request');
        if($request['error']) return ['error' => $request['error']];


        if(sizeof($request)) {

            $tariffList = $info->tariffList();

        }


        foreach($request as $tariffId => $tariff) {

            $tariffs[$tariffId]['name'] = $tariff['serviceName'];
            if($tariff['mode']) $tariffs[$tariffId]['mode'] = $tariff['mode'];
            else foreach($tariffList as $tariffListId => $tariffListItem) if($tariffListItem['id'] == $tariff['serviceCode']) {

                $tariffs[$tariffId]['mode'] = $tariffListItem['mode'];
                $tariffs[$tariffId]['name'] = $tariffListItem['name'];

            }
            $tariffs[$tariffId]['id'] = $tariff['serviceCode'];
            $tariffs[$tariffId]['DPMin'] = intval($tariff['days']);
            $tariffs[$tariffId]['DPMax'] = intval($tariff['days'])+1;
            $tariffs[$tariffId]['cost'] = $tariff['cost'];

        }

        //Способ доставки
        $query['selfDelivery'] = true; //если отправляем до дома то ставим значение false

        $request = $this->authorization->query($query, "calculator2?wsdl", "getServiceCost2", 'request');
        if($request['error']) return ['error' => $request['error']];

        foreach($request as $tariffId => $tariff) {

            if($tariff['serviceName']) $tariffs_p[$tariffId]['name'] = $tariff['serviceName'].' to point';
            if($tariff['mode']) $tariffs_p[$tariffId]['mode'] = $tariff['mode'];
            else foreach($tariffList as $tariffListId => $tariffListItem) if($tariffListItem['id'] == $tariff['serviceCode'].'_P') {

                $tariffs_p[$tariffId]['mode'] = $tariffListItem['mode'];
                $tariffs_p[$tariffId]['name'] = $tariffListItem['name'];

            }
            $tariffs_p[$tariffId]['id'] = $tariff['serviceCode'].'_P';
            $tariffs_p[$tariffId]['DPMin'] = intval($tariff['days']);
            $tariffs_p[$tariffId]['DPMax'] = intval($tariff['days'])+1;
            $tariffs_p[$tariffId]['cost'] = $tariff['cost'];

        }

        $tariffs = array_merge($tariffs, $tariffs_p);
        $tariffs = array_values($tariffs);


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffs;

        /*
            2.5.1.	Параметры входного сообщения для getServiceCost2

                Параметр	                                Описание	            Тип	                Обязательный	        Пример
                Внешний тэг	          request
                auth
                                      clientNumber	        Ваш клиентский номер в
                                                            системе DPD (номер
                                                            вашего договора с DPD)	Число	            Да	                    1000000000
                                      clientKey	            Ваш уникальный ключ
                                                            для авторизации,
                                                            полученный у сотрудника
                                                            DPD	                    Строка	            Да	                    1FD890C3556
                pickup
                                      cityId	            Идентификатор города
                                                            отправления	            Число	            Нет	                    49694102
                                      index	                Индекс	                Строка	            Нет	                    140012
                                      cityName	            Город  отправления	    Строка	            Нет	                    Москва (буквенные обозначения аббревиатур и других знаков)
                                      regionCode	        Код региона отправления	Число	            Нет	                    77
                                      countryCode	        Код страны отправления	Строка	            Нет	                    RU
                delivery
                                      cityId	            Идентификатор города
                                                            доставки	            Число	            Нет	                    49265227
                                      index	                Индекс	                Строка	            Нет	                    140012
                                      cityName	            Город  доставки	        Строка	            Нет                     Челябинск (буквенные обозначения аббревиатур и других знаков)
                                      regionCode	        Код региона доставки	Число	            Нет	                    74
                                      countryCode	        Код страны доставки	    Строка	            Нет	                    RU
                selfPickup		                            Самопривоз на терминал.	boolean	            Да	                    false
                selfDelivery		                        Доставка до терминала.
                                                            Самовывоз с терминала.	boolean	            Да	                    true
                weight		                                Вес отправки, кг	    Число	            Да	                    5
                volume		                                Объём, м3 	            Число	            Нет	                    0.05
                serviceCode		                            Список кодов услуг DPD.
                                                            Если параметр задан, то
                                                            сервис возвращает
                                                            стоимость только
                                                            заданных услуг. Если он
                                                            не задан – всех
                                                            доступных услуг.	    Строка

                                                                                    Список кодов
                                                                                    услуг через запятую	 Нет	                BZP,ECN
                pickupDate		                            Предполагаемая дата
                                                            приёма груза. Стоимость
                                                            будет считаться на
                                                            заданную дату. Если
                                                            параметр не задан – будет
                                                            считаться на текущую
                                                            дату.	                Дата	                Нет	                2014-05-21
                maxDays		                                Максимально допустимый
                                                            срок. Если параметр
                                                            задан, то все услуги
                                                            с большим сроком не
                                                            будут показываться.
                                                            Параметр не имеет
                                                            смысла, если задавать
                                                            конкретную услугу.	    Целое число	            Нет	                2
                maxCost		                                Максимально допустимая
                                                            стоимость. Если параметр
                                                            задан, то все услуги с
                                                            большей стоимостью не
                                                            будут показываться.
                                                            Параметр не имеет смысла,
                                                            если задавать конкретную
                                                            услугу.	                Число	                Нет	                (после запятой не более 2-х знаков)
                declaredValue		                        Объявленная ценность
                                                            груза	                Число	                Нет	                1000 (после запятой не более 2-х знаков)


        2.5.5.	Параметры ответа при успешном запросе
            Параметр	            Описание	            Тип	                Пример
            serviceСode	            Код услуги DPD	        Строка	            ECN
            serviceName	            Название услуги	        Строка	            DPD ECONOMY
            cost	                Стоимость услуги	    Число	            2,651.46
            days	                Срок доставки, дней	    Целое число	        2
            weight 	                Вес отправки, кг	    Число	            5
            volume1	                Объём, м3	            Число	            0.05

        */

    }
    public function create($putdata) { //Создание заказа на доставку

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['header']['datePickup'] = date("Y-m-d", strtotime($orders[0]["Pickup"]["date"]));
        $query['header']['pickupTimePeriod'] = "9-18";
        $query['header']['senderAddress']["name"] = $orders[0]["Pickup"]["FIO"];
        if($orders[0]["Pickup"]["Address"]["country"]) $query['header']['senderAddress']["countryName"] = $orders[0]["Pickup"]["Address"]["country"];
        if($orders[0]["Pickup"]["Address"]["index"]) $query['header']['senderAddress']["index"] = $orders[0]["Pickup"]["Address"]["index"];
        if($orders[0]["Pickup"]["Address"]["region"]) $query['header']['senderAddress']["region"] = $orders[0]["Pickup"]["Address"]["region"];
        if($orders[0]["Pickup"]["Address"]["city"]) $query['header']['senderAddress']["city"] = $orders[0]["Pickup"]["Address"]["city"];

        if($orders[0]["Pickup"]["Address"]["street"]) {

            $streetObj = $info->getStreetInfo($orders[0]["Pickup"]["Address"]["street"]);
            $street = $streetObj['name'] ?: $orders[0]["Pickup"]["Address"]["street"];
            if(!$street) $street = $orders[0]["Pickup"]["Address"]["street"];

        } else $street = 'нет';
        $query['header']['senderAddress']['street'] = $street;
        if($streetObj['type']["abbr"]) $query['header']['senderAddress']['streetAbbr'] = $streetObj['type']["abbr"];

        if($orders[0]["Pickup"]["Address"]["building"]) $query['header']['senderAddress']["house"] = intval($orders[0]["Pickup"]["Address"]["building"]);
        if($orders[0]["Pickup"]["Address"]["Housing"]) $query['header']['senderAddress']["houseKorpus"] = $orders[0]["Pickup"]["Address"]["Housing"];
        if($orders[0]["Pickup"]["Address"]["Apartment"]) $query['header']['senderAddress']["office"] = $orders[0]["Pickup"]["Address"]["Apartment"];

        $query['header']['senderAddress']['contactPhone'] = $orders[0]["Pickup"]["phone"];
        $query['header']['senderAddress']["contactFio"] = $orders[0]["Pickup"]["FIO"];


        foreach($orders as $orderId => $order) {

            $query['order'][$orderId]["orderNumberInternal"] = $order["orderNumber"];
            $query['order'][$orderId]["serviceCode"] = str_replace('_P', '', $order["tariff"]);

            if ($order["Pickup"]["selfPickup"]) {
                if ($order["toPoint"]) $query['order'][$orderId]["serviceVariant"] = "ТТ";
                else  $query['order'][$orderId]["serviceVariant"] = "ТД";
            } else {
                if ($order["toPoint"]) $query['order'][$orderId]["serviceVariant"] = "ДТ";
                else  $query['order'][$orderId]["serviceVariant"] = "ДД";
            }

            $query['order'][$orderId]["cargoNumPack"] = $order["cargoCount"];
            foreach($order["Places"] as $placeId => $place) $query['order'][$orderId]["cargoWeight"] += $place["Width"];
            $query['order'][$orderId]["cargoValue"] = $order["declaredValue"];
            $query['order'][$orderId]["cargoCategory"] = $order["description"];
            $query['order'][$orderId]["cargoRegistered"] = false;

            $query['order'][$orderId]["receiverAddress"]["name"] = $order["recipient"]["name"];

            if($order["toPoint"]) $query['order'][$orderId]["receiverAddress"]["terminalCode"] = $order["toPoint"];

            if($order["Address"]["country"]) $query['order'][$orderId]["receiverAddress"]["countryName"] = $order["Address"]["country"];
            if($order["Address"]["index"]) $query['order'][$orderId]["receiverAddress"]["index"] = $order["Address"]["index"];
            if($order["Address"]["region"]) $query['order'][$orderId]["receiverAddress"]["region"] = $order["Address"]["region"];
            if($order["Address"]["city"]) $query['order'][$orderId]["receiverAddress"]["city"] = $order["Address"]["city"];

            if($order["Address"]["street"]) {

                $streetObj = $info->getStreetInfo($order["Address"]["street"]);
                $street = $streetObj['name'] ?: $order["Address"]["street"];
                if(!$street) $street = $order["Address"]["street"];

            } else $street = 'нет';
            $query['order'][$orderId]["receiverAddress"]['street'] = $street;
            if($streetObj['type']["abbr"]) $query['order'][$orderId]["receiverAddress"]['streetAbbr'] = $streetObj['type']["abbr"];

            if($order["Address"]["building"]) $query['order'][$orderId]["receiverAddress"]['house'] = intval($order["Address"]["building"]);
            if($order["Address"]["Housing"]) $query['order'][$orderId]["receiverAddress"]['houseKorpus'] = $order["Address"]["Housing"];
            if($order["Address"]["Apartment"]) $query['order'][$orderId]["receiverAddress"]['flat'] = $order["Address"]["Apartment"];


            $query['order'][$orderId]["receiverAddress"]['contactEmail'] = $order["recipient"]["email"];
            $query['order'][$orderId]["receiverAddress"]['contactPhone'] = $order["recipient"]["phone"];
            $query['order'][$orderId]["receiverAddress"]["contactFio"] = $order["recipient"]["name"];


            if($order["Address"]["Info"]) $query['order'][$orderId]["receiverAddress"]["extraInfo"] = $order["Address"]["Info"];

            $query['order'][$orderId]["extraService"][] = [
                'esCode' => 'SMS',
                'param' => [
                    'name' => 'phone',
                    'value' => $order["recipient"]["phone"]
                ]
            ];


            if($order["payOnDelivery"]) {

                /*
                 * Отключено т.к. это тот кто инициатор отправки
                 */
                //$query['order'][$orderId]["paymentType"] = "ОУП"; //ОУП – оплата у получателя наличными //ОУО – оплата у отправителя наличными

                $query['order'][$orderId]["extraService"][] = [
                    'esCode' => 'НПП',
                    'param' => [
                        'name' => 'sum_npp',
                        'value' => $order["declaredValue"]
                    ]
                ];

                foreach ($order["Places"] as $placeId => $place) {

                    /*$query['order'][$orderId]['parcel'][$placeId]['number'] = '';
                    $query['order'][$orderId]['parcel'][$placeId]['weight'] = $place["Weight"];
                    $query['order'][$orderId]['parcel'][$placeId]['length'] = $place["Depth"];
                    $query['order'][$orderId]['parcel'][$placeId]['width'] = $place["Width"];
                    $query['order'][$orderId]['parcel'][$placeId]['height'] = $place["Height"];*/

                    foreach ($place["SubEncloses"] as $goodId => $good) {

                        $places["GoodsCode"]['article'] = $good["GoodsCode"];
                        if($good["name"]) $places["GoodsCode"]['descript'] = $good["name"];
                        $places["GoodsCode"]['npp_amount'] = $places["GoodsCode"]['npp_amount'] ? $places["GoodsCode"]['npp_amount']+$good["Price"] : $good["Price"];
                        $places["GoodsCode"]['count'] = $places["GoodsCode"]['count'] ? $places["GoodsCode"]['count']+$good["count"] : $good["count"];

                    }

                }

                foreach ($places as $placeId => $place) $places["GoodsCode"]['npp_amount'] = $places["GoodsCode"]['npp_amount']/$places["GoodsCode"]['count'];

                $places = array_values($places);

                $query['order'][$orderId]["unitLoad"] = $places;

            }

        }


        $request = $this->authorization->query($query, "order2?wsdl", "createOrder", "orders");
        if($request['error']) return ['error' => $request['error']];


        foreach($request as $orderId => $order) {

            if(!$order['errorMessage']) {

                $response['CreatedSendings'][$orderId]['InvoiceNumber'] = $order['orderNum'];
                $response['CreatedSendings'][$orderId]['orderNumber'] = $order["orderNumberInternal"];
                $response['CreatedSendings'][$orderId]['status'] = $order["status"];

            } else {

                $response['RejectedSendings'][$orderId]['orderNumber'] = $order["orderNumberInternal"];
                //$response['RejectedSendings'][$orderId]['ErrorCode'] = $response[$queryId]->errors[0]->code;
                $response['RejectedSendings'][$orderId]['ErrorMessage'] = $order['errorMessage'];

            }

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
         3.5.1.	Параметры входного сообщения createOrder
            Параметр	                            Описание	                Тип	                Обязательный	            Пример
            Внешний тэг	        orders
            auth
                                clientNumber	    Ваш клиентский номер в
                                                    системе DPD (номер вашего
                                                    договора с DPD)	            Число	            Да	                        1001028502
                                clientKey	        Ваш уникальный ключ для
                                                    авторизации, полученный у
                                                    сотрудника DPD	            Строка	            Да	                        1FD890C3556
            header		                            Информация, содержащаяся
                                                    в этом параметре, является
                                                    общей для всех заказов
                                                    в запросе
                                datePickup	        Дата приёма груза	        Дата	            Да	                        2016-07-26
                                payer	            Клиентский номер
                                                    плательщика в системе DPD
                                                    (номер договора с DPD).
                                                    Если этот параметр не
                                                    заполнен, то плательщиком
                                                    будет считаться заказчик
                                                    (номер из параметра auth).	Число	            Нет	                        1000000000
                                senderAddress	    Адрес приёма груза	        address
                                                                                                    Да
                                pickupTimePeriod    Интервал времени приёма
                                                    груза. Доступные для выбора
                                                    интервалы приёма см.
                                                    в разделе «Интервалы
                                                    времени приёма».
                                                                                Строка	            Да	                        9-18
                                regularNum	        Номер регулярного заказа
                                                    DPD. Если вы используете
                                                    доставку на регулярной
                                                    основе, уточните этот
                                                    номер у своего менеджера.	Строка	            Нет	                        1000
             order		                            Массив данных, относящихся
                                                    к каждому конкретному заказу
                                orderNumberInternal	Номер заказа в
                                                    информационной системе
                                                    клиента	                    Строка	            Да	                        123456
                                serviceCode	        Код услуги DPD. Уточните
                                                    код нужной Вам услуги у
                                                    своего менеджера или
                                                    используйте код услуги,
                                                    полученный из веб-сервиса
                                                    «Калькулятор стоимости»	    Строка	            Да	                        CUR
                                serviceVariant	    Вариант доставки. Доступно
                                                    4 варианта: ДД, ДТ, ТД и ТТ.
                                                    Расшифровку вариантов см.
                                                    в разделе «Варианты доставки».
                                                                                Строка	            Да	                        ДД
                                cargoNumPack	    Количество грузомест
                                                    (посылок) в отправке 	    Целое число	        Да	                        5
                                cargoWeight	        Вес отправки, кг	        Число	            Да	                        5
                                cargoVolume	        Объём, м3 	                Число	            Нет	                        0.05(после точки не более 2-х знаков)
                                cargoRegistered	    Ценный груз. Внутреннее
                                                    вложение, включенное в
                                                    перечень товаров, требующих
                                                    дополнительных мер безопасности,
                                                    снижающих риск его утери
                                                    или повреждения при
                                                    перевозке.

                                                    Перечень товаров,
                                                    относимых к категории
                                                    «Ценный груз»:
                                                    1. Мобильные телефоны
                                                    2. Ноутбуки, планшеты	    boolean	            Да	                        false
                                cargoValue	        Сумма объявленной
                                                    ценности, руб.	            Число	            Нет	                        1000 (после точки не более 2-х знаков)
                                cargoCategory	    Содержимое отправки	        Строка	            Да	                        Одежда
                                deliveryTimePeriod	Интервал времени доставки
                                                    груза. Доступные для выбора
                                                    интервалы доставки см.
                                                    в разделе «Интервалы времени
                                                    доставки».
                                                                                Строка	            Нет	                        9-18
                                paymentType*	    Форма оплаты
                                                    «Возможные варианты оплаты»
                                                                                Строка	            Нет	                        ОУП
                                extraParam	        Зарезервированный параметр
                                                    для ввода новых
                                                    параметров без изменения
                                                    схемы сервиса	            parameter
                                                                                                    Нет
                                dataInt	            Данные для международных
                                                    отправок 	                dataInternational
                                                                                                    Нет
                                receiverAddress	    Адрес доставки	            address
                                                                                                    Да
                                extraService	    Массив опций доставки	    extraService
                                                                                                    Нет
                                parcel	            Массив посылок отправки
                                                    (указывать только при
                                                    работе по своим
                                                    штрих-кодам) 	            parcel
                                                                                                    Нет
                                unitLoad	        Массив вложений в посылке
                                                    для 54ФЗ(только для
                                                    доставки по России)	        unitLoad
                                                                                                    нет

        *- Если данный параметр не указывать, то счёт будет выставлен автоматически на отправителя по безналичному расчёту.


        3.5.3.	Параметры ответного сообщения createOrder \ getOrdersStatus
            Параметр	                            Описание	                Тип	                Обязательный	        Пример
            orderNumberInternal	                    Номер заказа в
                                                    информационной системе
                                                    клиента	                    Строка	            Да	                    123456
            orderNum	                            Номер заказа DPD.
                                                    Возвращается в ответном
                                                    сообщении.	                Строка	            Нет	                    01010001MOW
            status	                                Статус создания заказа.
                                                    Возвращается в
                                                    ответном сообщении.
                                                    Возможные статусы
                                                    перечислены в разделе
                                                    «Статусы создания заказа».
                                                                                Строка	            Да	                    OK
            errorMessage	                        Текст ошибки	            Строка	            Нет	                    Не заполнен параметр «Улица»



         */

    }
    public function edit($orderNumber, $invoiceNumber='', $recipientName='', $recipientPhone='', $recipientEmail='', $declaredValue='', $toPoint='', $addressIndex='', $addressCountry='', $addressRegion='', $addressCity='', $addressStreet='', $addressBuilding='', $addressHousing='', $addressApartment='', $addressPorch='', $addressFloor='', $addressInfo='') { //Изменение заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        /*$request = $this->authorization->query($query, "application.wadl", "getShipmentList", "request");
        if($request['error']) return ['error' => $request['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;*/

        /*
         10.5.	Входящее сообщение метода getShipmentList
            Параметр	                Описание	            Тип	            Обязательный	        Пример
                request
            Auth
                clientNumber	        Уникальный клиентский
                                        номер для доступа к
                                        веб-службе «Управление
                                        доставкой»
                                                                Число	        Да	                    3355779903

                clientKey	            Уникальный ключ доступа
                                        к веб-службе
                                        «Управление доставкой»
                                                                Строка	        Да	                    7BC65C96D4DE98CD17072C88BFE21CC703A38607

            eshopOrderNum		        Номе заказа в
                                        Интернет-Магазине
                                        (Номер заказа в ИС
                                        клиента)	            Строка	        Нет	                    1234567890
            phone		                Номер телефона
                                        получателя и/или
                                        номер телефона для
                                        SMS оповещения	        Строка	        Да	                    89260161212
            email		                Email номер клиента.	Строка	        Нет	                    12345@mail.ru
            orderNum		            Номер заказа DPD.
                                        Возвращается в
                                        ответном сообщении.	    Строка	        Нет	                    05120002MOW
                                        ПРИМЕЧАНИЕ. Для
                                        авторизации необходимо
                                        использовать следующие
                                        данные:
                                        clientNumber – 3355779903,
                                        clientKey - 7BC65C96D4DE98CD17072C88BFE21CC703A38607


         10.7.	Методы сохранения  данных saveAddress
            Параметр	                Описание	            Тип	            Обязательный	        Пример
                request
            Auth
                sessionId	            Идентификатор сессии,
                                        полученный методом
                                        getShipmentList
                                                                Число	        Да
                orderId	                Идентификатор заказа,
                                        полученный методом
                                        getShipmentList
                                                                Число	        Да	                    23854956
                streetID	            Идентификатор улицы 	Число	        Нет
                StreetName	            Наименование улицы	    Строка      	Да
                building	            Номер дома	            Строка	        Нет
                Str	                    Номер строения	        Строка	        Нет
                Korp	                Номер корпуса	        Строка	        Нет
                Flat	                Номер квартиры	        Строка	        Нет
                contractor	            Наименование
                                        получателия	            Строка	        Да
                contact	                Контактное лицо
                                        получателя	            Строка	        Да
                courierInstruction	    Комментарий для
                                        курьера	                Строка	        Нет
                needPass	            Признак необходимости
                                        пропуска для курьера
                                        на въезд на территорию
                                        получателя	            Целое число	    Нет
                phone	                Номер телефона
                                        получателя	            Строка	        Да
                oldContact	            Старое контактное
                                        лицо получателя
                oldAddress	            Старый адресс	        Строка	        Нет



        10.9.	Метод  подтверждение изменения пункта выдачи  SaveParcelShop
                Параметр	            Описание	            Тип	            Обязательный	    Пример
                    request
                Auth
                    sessionId	        Идентификатор сессии,
                                        полученный методом
                                        getShipmentList
                                                                Число	        Да
                    orderId	            Идентификатор заказа,
                                        полученный методом
                                        getShipmentList
                                                                Число	        Да	                23854956
                    Departmetnt_ID	    Идентификатор терминала	Число	        Да	                23854957

         */

    }
    public function getInfo($orderNumber='', $invoiceNumber='') { //Отслеживание статуса и информация о доставке

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if($invoiceNumber) {

            $query['dpdOrderNr'] = $invoiceNumber;

            $request = $this->authorization->query($query, "tracing?wsdl", "getStatesByDPDOrder", 'request');
            if($request['error']) return ['error' => $request['error']];

            /*
               5.4.8.	getStatesByDPDOrder
                Параметр	                            Описание	                Тип	            Обязательный	        Пример
                Внешний тэг	request
                auth
                                clientNumber	        Ваш клиентский номер в
                                                        системе DPD (номер вашего
                                                        договора с DPD)	            Число	        Да	                    1000000000
                                clientKey	            Ваш уникальный ключ для
                                                        авторизации, полученный у
                                                        сотрудника DPD	            Строка	        Да	                    1FD890C3556
                dpdOrderNr	                            Номер заказа в
                                                        информационной системе DPD	Строка	        Да	                    04040001MOW
                pickupYear	                            Год заказа (т.к. номера
                                                        заказов DPD уникальные
                                                        в пределах года, требуется
                                                        уточнение, чтобы получить
                                                        однозначный результат)	    Целое число	    Нет	                    2012

            */

        } elseif($orderNumber) {

            $query['clientOrderNr'] = $orderNumber;

            $request = $this->authorization->query($query, "tracing?wsdl", "getStatesByClientOrder", 'request');
            if($request['error']) return ['error' => $request['error']];

            /*

               5.4.5.	getStatesByClientOrder
                Параметр	                            Описание	                Тип	            Обязательный	        Пример
                Внешний тэг	request
                auth
                                clientNumber	        Ваш клиентский номер в
                                                        системе DPD (номер
                                                        вашего договора с DPD)	    Число	        Да	                    1000000000
                                clientKey	            Ваш уникальный ключ для
                                                        авторизации, полученный
                                                        у сотрудника DPD	        Строка	        Да	                    1FD890C3556
                clientOrderNr	                        Номер заказа в
                                                        информационной системе
                                                        клиента	                    Строка	        Да	                    12346DPD
                pickupDate	                            Дата приёма груза
                                                        (на случай, если номер
                                                        заказа не уникален,
                                                        и требуется уточнение
                                                        по дате)	                Дата	        Нет	                    2014-02-28

            */

        }


        $statusList = $info->statusList();


        $response['status']['State'] = $request['states'][0]['newState'];
        $response['status']['ChangeDT'] = $request['states'][0]['transitionTime'];

        foreach($statusList as $status) if($status['State'] == $response['status']['State']) $response['status']['StateMessage'] = $status['StateText'];


        $response['info']['InvoiceNumber'] = $request['states'][0]['dpdParcelNr'];
        $response['info']['SenderInvoiceNumber'] = $request['states'][0]['clientOrderNr'];
        $response['info']['Sum'] = $request['states'][0]['orderCost'];
        $response['info']['CreateDate'] = $request['docDate'];
        //$response['info']['FIO'] = $request['departure']['address']['receiver'];
        $response['info']['StorageDate'] = $request['states'][0]['pickupDate'];
        //$response['info']['tariff'] = $request['departure']['shipping_method']['id'];
        //$response['info']['address'] = $request['departure']['address'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
       Ответ

        Параметр	                            Описание	                Тип	            Пример

        docId	                                Идентификатор документа.
                                                Данный идентификатор
                                                используется для
                                                подтверждения получения
                                                статусов	                Число	        12346897
        docDate	                                Дата формирования документа	Дата	        2014-02-28
        clientNumber	                        Ваш клиентский номер в
                                                системе DPD	                Число	        1000000000
        resultComplete	                        Показывает, выбраны ли в
                                                текущем запросе все
                                                новые состояния по клиенту
                                                (значение true), или был
                                                достигнут лимит записей
                                                в одном запросе и для
                                                продолжения необходим
                                                ещё один запрос (значение
                                                false).
                                                Пояснение.

                                                Если возвращается false,
                                                то значит есть ещё
                                                статусы и можно повторно
                                                вызвать метод, чтобы
                                                их получить. В этом
                                                случае нет ограничения
                                                на повторный вызов.

                                                Если же в ответе вернулось
                                                true – то значит больше
                                                статусов нет и повторный
                                                вызов возможен только
                                                через 5мин.
                                                                            boolean 	    true
        states		                            Массив состояний посылок
                        clientOrderNr	        Номер заказа в
                                                информационной системе
                                                клиента	                    Строка	        12346DPD
                        clientParcelNr	        Номер посылки в
                                                информационной системе
                                                клиента	                    Строка	        12346897
                        dpdOrderNr	            Номер заказа в
                                                информационной системе
                                                DPD	                        Строка	        04040001MOW
                        dpdParcelNr	            Номер посылки в
                                                информационной системе
                                                DPD	                        Строка	        12346897
                        pickupDate	            Дата приёма груза	        Дата 	        2014-02-28
                        dpdOrderReNr	        Номер повторного заказа
                                                в системе DPD (заполняется
                                                в том случае, если
                                                по одному и тому
                                                же клиентскому номеру
                                                посылки в системе
                                                DPD существует
                                                два заказа – например,
                                                при заказе на возврат
                                                посылки)	                Строка	        04040002MOW
                        dpdParcelReNr	        Номер посылки при
                                                повторном заказе в
                                                системе DPD (заполняется
                                                в том случае, если
                                                по одному и тому же
                                                клиентскому номеру
                                                посылки в системе
                                                DPD существует два
                                                заказа – например, при
                                                заказе на возврат
                                                посылки)	                Строка	        12346899
                        isReturn 	            признак «Возвратная
                                                посылка»	                Логический	    true/false
                        planDeliveryDate	    Планируемая дата
                                                доставки посылки	        Дата	        2014-03-01
                    *	orderPhysicalWeight     Физический вес (кг)
                                                отправки 	                Число	        2.08
                    *	orderVolume 	        Объем (м3) отправки 	    Число	        0.023
                    *	orderVolumeWeight 	    Объемный вес (кг.)
                                                отправки 	                Число	        4.6
                    *	orderPayWeight 	        Платный вес  (кг.)
                                                отправки	                Число	        5
                    *	orderCost 	            Сумма за доставку, без
                                                НДС	                        Число	        1200 (после запятой не более 2-х знаков)
                    *	parcelPhysicalWeight    Физический вес (кг.)	    Число	        1.5
                    *	parcelVolume 	        Объем (м3)	                Число	        0.008
                    *	parcelVolumeWeight 	    Объемный вес (кг.)
                                                посылки	                    Число	        1.5
                    *	parcelPayWeight 	    Платный вес (кг.)
                                                посылки	                    Число	        1.5
                    *	parcelLength 	        Длина (см.) посылки	        Число	        157
                    *	parcelWidth 	        Ширина (см.) посылки	    Число	        7
                    *	parcelHeight 	        Высота (см.) посылки	    Число	        7
                        newState	            Состояние посылки после
                                                перехода. См. список
                                                возможных состояний
                                                в разделе «Состояния»
                                                пункт  «Посылка».
                                                                            Строка	        Delivering
                        transitionTime	        Время перехода состояния	Дата/время	    2012-04-04T17:10:15
                        terminalCode	        Код терминала DPD, на
                                                котором произошел
                                                переход состояния	        Строка	        LED
                        terminalCity	        Город терминала DPD, на
                                                котором произошел
                                                переход состояния	        Строка	        LED
                        incidentCode	        Код инцидента,
                                                произошедшего при
                                                переходе состояния.
                                                Список возможных
                                                кодов инцидентов
                                                и их расшифровок вы
                                                можете получить у
                                                своего менеджера.	        Строка	        90
                        incidentName	        Наименование инцидента,
                                                 произошедшего при
                                                переходе состояния	        Строка	        Возвращено отправителю
                        consignee	            Фактический получатель
                                                посылки (передается
                                                только со статусом
                                                Delivered)	                Строка	        Иванов И.И.


        * получение данной информации возможно по URL:
        http://wstest.dpd.ru:80/services/tracing1-1?xsd=1 или http://ws.dpd.ru:80/services/tracing1-1?xsd=1

        */

    }
    public function cancel($orderNumber='', $invoiceNumber='') { //Отмена заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;



        if($orderNumber) $query['cancel']['orderNumberInternal'] = $orderNumber;
        if($invoiceNumber) $query['cancel']['orderNum'] = $invoiceNumber;

        $request = $this->authorization->query($query, "order2?wsdl", "cancelOrder", "orders");
        if($request['error']) return ['error' => $request['error']];

        if($request['status'] == 'Cancelled' || $request['status'] == 'CancelledPreviously') $response['Result'] = true;
        else {

            $response['Result'] = false;
            $response['Error'] = $request['errorMassage'];
            $response['ErrorCode'] = $request['status'];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
         8.5.	cancelOrder Входящие сообщения
            Параметр	                    Описание	                Тип	                Обязательный	            Пример
            Внешний тэг	orders
            Auth
                        clientNumber	    Ваш клиентский номер в
                                            системе DPD (номер
                                            вашего договора с DPD)	    Число	            Да	                        1000000000
                        clientKey	        Ваш уникальный ключ для
                                            авторизации, полученный
                                            у сотрудника DPD	        Строка	            Да	                        1FD890C3556
            cancel
                        orderNumberInternal	Номер заказа в
                                            информационной системе
                                            клиента	                    Строка	            Да*	                        123456
                        orderNum	        Номер заказа DPD	        Строка	            Да*	                        05120002MOW
                        pickupdate	        Дата приема груза	        Дата	            Нет	                        2014-12-05

            *Обязательно должен быть указан один из параметров «Номер заказа клиента» или «Номер заказа DPD»



         8.6.	Параметры ответного сообщения
            Параметр	                    Описание	                Тип	                Обязательный	            Пример
            orderNumberInternal	            Список посылок	            Строка	            Нет
            orderNum	                    Номер заказа DPD.
                                            Возвращается в ответном
                                            сообщении.	                Строка	            Нет	                        01010001MOW
            status	                        Статус изменения заказа.
                                            Возвращается в ответном
                                            сообщении. Возможные
                                            статусы перечислены в
                                            разделе «Статусы»
                                                                        Строка	            Да	                        Canceled
            errorMassage	                Текст сообщения об ошибке
                                                                        Строка



         8.7.	Статусы
            Код Status	                Описание
            Cancelled	                Операция выполнена успешно
            CancelledPreviously	        Отменено ранее
            CallDPD	                    Состояние заказа не позволяет отменить заказ самостоятельно, для отмены заказа необходим звонок в Конткат-Центр.
            NotFound	                Данные не найдены
            Error	                    Текст сообщения об ошибке




         8.8.	Текст сообщений об ошибке

            Отмена заказа невозможна, возникла ошибка:  <Описание проблемы>
            Описание проблемы:
            •	Получены не все данные на вход
            •	Полученные данные некорректны
            •	Не найден заказ \ заявка \ отправка

         */

    }

}
