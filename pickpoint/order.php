<?php

namespace pickpoint;

class order extends \abstracts\order {

    public function getCost($fromCity, $fromRegion='', $toCity='', $toRegion='', $toPoint='', $index='', $length='', $depth='', $width='',$weight=1, $count=1, $declaredValue=0, $invoiceNumber='') { //Расчёт стоимости доставки

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(!$toPoint) {


            $points = $info->parcelShopsList($toCity, $toRegion);
            $toPoint = $points[0]['Number'];

        }

        $query["SessionId"] = $this->authorization->sessionId;
        $query["IKN"] = $this->authorization->ikn;
        $query["InvoiceNumber"] = $invoiceNumber;
        $query["FromCity"] = $fromCity;
        $query["FromRegion"] = $fromRegion;
        $query["PTNumber"] = $toPoint;
        $query["EncloseCount"] = $count ?: 1;
        $query["Length"] = $length;
        $query["Depth"] = $depth;
        $query["Width"] = $width;
        $query["Weight"] = $weight;

        //print_r($query);

        $request = $this->authorization->query(json_encode($query), "calctariff", "POST");
        if($request['error']) return ['error' => $request['error']];

        $tariffs[0]['name'] = 'Доставка до ПВЗ';
        $tariffs[0]['mode'] = 'Д-С';
        $tariffs[0]['id'] = '1';
        $tariffs[0]['DPMin'] = $request['DPMin'];
        $tariffs[0]['DPMax'] = $request['DPMax'];
        foreach($request['Services'] as $service) {
            $tariffs[0]['cost'] += $service['Tariff'];
        }
        $tariffs[0]['info'] = $request;


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffs;

        /*
        Расчет тарифа
        URL: /calctariff
        Метод: POST
        Описание
        Команда предназначена для получения стоимости доставки. При расчете учитываются следующие ограничения:
        •	габариты указываются общие на все места,
        •	вес по умолчанию считается 1 кг,
        •	рассчитывается только тариф за логистику.
        ВНИМАНИЕ!
        •	Данная функция работает только на Рабочей версии сервиса
        https://e-solution.pickpoint.ru/api/
        Структура запроса
        {
        "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
        "IKN":		<Номер контракта>,
        "InvoiceNumber":	<Номер отправления, не обязательное поле>,
        "FromCity":	<Город сдачи отправления>,
        "FromRegion":	<Регион города сдачи отправления>,
        "PTNumber":	<Пункт выдачи (назначения) отправления>,
        "EncloseCount":	<Количество мест, по умолчанию одно, не обязательное поле>,
        "Length":		<Длина отправления, см>,
        "Depth":		<Глубина отправления, см>,
        "Width":		<Ширина отправления, см>,
        "Weight":		<Вес отправления, не обязательное поле, по умолчанию 1кг>
        }

        Структура ответа
        {
        "SessionId":	"<уникальный идентификатор сессии  (GUID 16 байт)>",
        "Services":
         [
                {
                    "name":	"<Наименование тарифа>",
                    "Tariff"	:"<Стоимость доставки по тарифу>",
            "NDS":	"<НДС>"
        }
        ],
        "InvoiceNumber":	<Номер накладной>,
        "DPMin":  	<Минимальный срок доставки>,
        "DPMax" :	<Максимальный срок доставки>,
        "Zone":	                <Зона>,
            "ErrorCode":	<Код ошибки: 0 – нет ошибки, -1 - ошибка>,
        "ErrorMessage":	"<Описание ошибки, (200 символов)>"
        }
        */

    }
    public function create($putdata) { //Создание заказа на доставку

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //print_r($orders);

        //Многоместное отправление

        $query["SessionId"] = $this->authorization->sessionId;

        foreach($orders as $orderId => $order) {

            $query["Sendings"][$orderId]["EDTN"] = $orderId;
            $query["Sendings"][$orderId]["IKN"] = $this->authorization->ikn;
            $query["Sendings"][$orderId]["Invoice"]["SenderCode"] = $order["orderNumber"];
            $query["Sendings"][$orderId]["Invoice"]["Description"] = $order["description"];
            $query["Sendings"][$orderId]["Invoice"]["RecipientName"] = $order["recipient"]["name"];
            $query["Sendings"][$orderId]["Invoice"]["MobilePhone"] = $order["recipient"]["phone"];
            $query["Sendings"][$orderId]["Invoice"]["Email"] = $order["recipient"]["email"];
            $query["Sendings"][$orderId]["Invoice"]["PostageType"] = $order["payOnDelivery"] ? 10003 : 10001;
            $query["Sendings"][$orderId]["Invoice"]["GettingType"] = $order["Pickup"]["selfPickup"] ? 102 : 101;
            $query["Sendings"][$orderId]["Invoice"]["PayType"] = 1;
            $query["Sendings"][$orderId]["Invoice"]["Sum"] = $order["declaredValue"];
            $query["Sendings"][$orderId]["Invoice"]["InsuareValue"] = 0;
            $query["Sendings"][$orderId]["Invoice"]["PostamatNumber"] = $order["toPoint"];
            if($order["Places"]) $query["Sendings"][$orderId]["Invoice"]["Places"] = $order["Places"];

        }

        $response = $this->authorization->query(json_encode($query), "CreateShipment", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];

        foreach($response['CreatedSendings'] as $orderId => $order) {

            $response['CreatedSendings'][$orderId]['orderNumber'] = $query["Sendings"][$orderId]["Invoice"]["SenderCode"];

        }
        foreach($response['RejectedSendings'] as $orderId => $order) {

            $response['RejectedSendings'][$orderId]['orderNumber'] = $query["Sendings"][$orderId]["Invoice"]["SenderCode"];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Регистрация отправлений (многоместных)
        URL: /CreateShipment
        Метод: POST
        Описание
        Команда предназначена для регистрации многоместных отправлений т.е. отправления состоящие из 2-х и более коробок/пакетов. На вход принимается структура, содержащая номер сессии и список описаний отправлений, которые требуется зарегистрировать.

        Структура запроса
        {
            "SessionId":"<уникальный идентификатор сессии (GUID 16 байт)>",
            "Sendings":
            [
                {
                    "EDTN":		"<Идентификатор запроса, используемый для ответа. Указывайте уникальное число (50 символов)>",
                    "IKN": 		"<ИКН – номер договора (10 символов)>",
                    "Invoice":
                    {
                        "SenderCode":	"<Номер заказа магазина (50 символов)>",
                        ‘Description":	"<Описание отправления, обязательное поле (200 символов)>",
                        "RecipientName":	"<Имя получателя (150 символов)>",
                        "PostamatNumber":	"<Номер постамата, обязательное поле (8 символов)>",
                        "MobilePhone": 	"<один номер телефона получателя, обязательное поле(100 символов)>",
                        "Email": 		"<Адрес электронной почты получателя (256 символов)>",
                        "PostageType": 	<Тип услуги, (см. таблицу ниже) обязательное поле >,
                        "GettingType": 	<Тип сдачи отправления, (см. таблицу ниже) обязательное поле >,
                        "PayType": 	<Тип оплаты, (см. таблицу ниже) обязательное поле >,
                        "Sum": 		<Сумма, обязательное поле (число, два знака после запятой)>,
                        "InsuareValue":	<Страховка (число, два знака после запятой)>,
                        "ClientReturnAddress":	"<Адрес клиентского возврата>" Данный блок можно не передавать. Если передаете, то необходимо заполнение всех полей блока.
                        {
                            "CityName":	"<Название города (50 символов)>",
                            "RegionName":	"<Название региона (50 символов)>",
                            "Address":	"<Текстовое описание адреса (150 символов)>",
                            "FIO":		"<ФИО контактного лица (150 символов)>",
                            "PostCode":	"<Почтовый индекс (20 символов)>",
                            "Organisation":	"<Наименование организации (100 символов)>",
                            "PhoneNumber":	"<Контактный телефон, обязательное поле (допускаются круглые скобки и тире)>",
                            "Comment":	"<Комментарий (255 символов)>"
                        },
                        "UnclaimedReturnAddress":	"<Адрес возврата невостребованного >" Данный блок можно не передавать. Если передаете, то необходимо заполнение всех полей блока.
                        {
                            "CityName":	"<Название города (50 символов)>",
                            "RegionName":	"<Название региона (50 символов)>",
                            "Address":	"<Текстовое описание адреса (150 символов)>",
                            "FIO":		"<ФИО контактного лица (150 символов)>",
                            "PostCode":	"<Почтовый индекс (20 символов)>",
                            "Organisation":	"<Наименование организации (100 символов)>",
                            "PhoneNumber":	"<Контактный телефон, обязательное поле (допускаются круглые скобки и тире)>",
                            "Comment":	"<Комментарий  (255 символов)>"
                        },
                        "Places":
                        [
                            {
                                "BarCode": 	"<Штрих код от PickPoint. Отправляйте поле пустым, в ответ будет ШК (50 символов)>",
                                "GCBarCode":	"<Клиентский штрих-код. Поле не обязательное. Можно не отправлять (255 символов)>",
                                "Width": 		<Ширина, обязательное поле (число, два знака после запятой)>,
                                "Height": 		<Высота, обязательное поле (число, два знака после запятой)>,
                                "Depth": 		<Глубина, обязательное поле (число, два знака после запятой)>>,
                                "SubEncloses":	<Субвложимые>
                                [
                                    {
                                        "Line":		"<Номер>"1,
                                        "ProductCode":	"<Код продукта>"1,
                                        "GoodsCode":	"<Код товара>"1,
                                        "name":		"<Наименование>"1,
                                        "Price":		<Стоимость>1
                                    }
                                ]
                            }
                        ]
                    }
                }
            ]
        }

        (1) – Длина полей: (len(Line) + len(ProductCode) + len(GoodsCode) + len(Name) + 3) <= 255
        Описание полей:
        SenderCode	Номер заказа магазина. строка 50 символов
        BarCode	Значение Штрих Кода. Если вам не выделили диапазон ШК, отправляйте поле пустым
        Description	Описание типа вложимого, Пример: «Одежда и Обувь». строка 200 символов, обязательное поле
        RecipientName	Имя получателя, строка 60 символов, обязательное поле
        PostamatNumber	Номер постамата, вида XXXX-XXX, обязательное поле
        MobilePhone	Один номер Мобильного телефона получателя для SMS, желателен формат номера: 7/8хххХХХххХХ, при этом допускаются разделители (, ), -, пробелы обязательное поле
        Email	Email, строка 256 символов
        PostageType	Вид отправления, обязательное поле
            10001	Стандарт. Оплаченный заказ. При этом поле «Sum=0»
            10002	Приоритет  - НЕ ИСПОЛЬЗУЕТСЯ
            10003	Стандарт НП Отправление с наложенным платежом. Поле «Sum>0»
            10004	Приоритет НП  - НЕ ИСПОЛЬЗУЕТСЯ
        GettingType	Тип сдачи отправления, обязательное поле
            101	вызов курьера. Наш курьер приедет к вам за отправлениями.
            102	в окне приема СЦ Вы привезете отправления в филиал PickPoint
            103	в окне приема ПТ валом - НЕ ИСПОЛЬЗУЕТСЯ
            104	в окне приема ПТ (самостоятельный развоз в нужный ПТ + при создании отправления у ПТ - С2С) - НЕ ИСПОЛЬЗУЕТСЯ
        PayType	Всегда 1
        Sum	Сумма к оплате. обязательное поле См.п. «PostageType»
        InsuareValue	Сумма страховки - НЕ ИСПОЛЬЗУЕТСЯ
        Width	Ширина, в см, обязательное поле Если не знаете точных габаритов указывайте примерные значения или =0. (при типе сдаче в окне приема ПТ (валом или по ячейкам).
        Height	Высота, в см, обязательное поле Если не знаете точных габаритов указывайте примерные значения или =0
        Depth	Глубина в см, обязательное поле Если не знаете точных габаритов указывайте примерные значения или =0

        Структура ответа
        {
            "CreatedSendings":
            [
                {
                    "EDTN":		"< Значение идентификатора запроса (50 символов)>",
                    "InvoiceNumber":	"<Номер отправления присвоенный PickPoint (20 символов)>",
                    "Places":
                    [
                        {
                            "GCBarCode":	"<Клиентский номер ШК (255 символов, если есть во входящем запросе)>",
                            "Barcode":	"< Штрих код от PickPoint (50 символов, генерируется, если не было во входящем запросе)>"
                        }
                    ]
                }
            ]
            "RejectedSendings":
            [
                {
                    "EDTN":		"< Значение идентификатора запроса (50 символов)>",
                    "ErrorCode":	<Код ошибки, цифра >,
                    "ErrorMessage":	"<Описание ошибки, (200 символов)>"
                }
            ]
        }
        */
    }
    public function edit($orderNumber, $invoiceNumber='', $recipientName='', $recipientPhone='', $recipientEmail='', $declaredValue='', $toPoint='', $addressIndex='', $addressCountry='', $addressRegion='', $addressCity='', $addressStreet='', $addressBuilding='', $addressHousing='', $addressApartment='', $addressPorch='', $addressFloor='', $addressInfo='') { //Изменение заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //updateInvoice PickPoint Обновление полей созданного отправления

        $query["SessionId"] = $this->authorization->sessionId;
        if($invoiceNumber) $query["InvoiceNumber"] = $invoiceNumber;
        else $query["GCInvoiceNumber"] = $orderNumber;
        if($toPoint) $query["PostamatNumber"] = $toPoint;
        elseif($addressIndex || $addressCountry || $addressRegion || $addressCity || $addressStreet || $addressBuilding || $addressHousing || $addressApartment || $addressPorch || $addressFloor || $addressInfo) {
            return $response['error'] = 'Служба PickPoint не поддерживает доставку до адреса!';
        }
        if($recipientPhone) $query["Phone"] = $recipientPhone;
        if($RecipientName) $query["RecipientName"] = $RecipientName;
        if($recipientEmail) $query["Email"] = $recipientEmail;
        if($declaredValue) $query["Sum"] = $declaredValue;

        //print json_encode($query);

        $response = $this->authorization->query(json_encode($query), "updateInvoice", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Обновление полей созданного отправления
        URL: /updateInvoice
        Метод: POST
        Описание
        Команда предназначена для изменения полей созданного отправления. Обновлять возможно следующие поля: номер постамата для доставки, номер телефона получателя, имя получателя, почтовый ящик получателя, сумму к оплате. Для того, чтобы поле было корректно обновлено, отправление должно находиться в статусе, при котором редактирование данного поля разрешено:

        text								Status	"PostamatNumber"			"MobilePhone"	"RecipientName":	"Email":	"Sum":
        Зарегистрирован						101		ок							ок				ок					ок			ок
        Сформирован для передачи Логисту	102		ок							ок				ок					ок			ок
        Развоз до ПТ самостоятельно			103		ок							ок				ок					ок			ок
        Сформирован для отправки			104		ок							ок				ок					ок			ок
        Принят Логистом						105		no							ок				ок					ок			ок
        На кладовке Логиста					106		ок (in region)				ок				ок					ок			ок
        Выдан на маршрут					107		ок (in region)				ок				ок					ок			ок
        Выдано курьеру						108		ок (APT only; in region;)	ок				ок					ок			ок
        Доставлено в ПТ						109		no							ок				ок					ок			ок
        Получен								111		no							no				ок					ок			no
        Невостребованное					112		no							no				no					no			no
        Возвращено Клиенту					113		no							no				no					no			no
        Отказ								114		no							no				no					no			no
        Сформирован возврат					115		no							no				no					no			no
        Передано на возврат					116		no							no				no					no			no
        Передано в логистическую компанию	117		no							no				no					no			no
        Сконсолидировано					123		no							no				no					no			no

        Если необязательное поле не должно быть изменено, не следует заполнять его в запросе или передавайте в качестве его значения null.
        Структура запроса
        {
            "SessionId": "<уникальный идентификатор сессии>",
            "InvoiceNumber": "<Номер КО>",
            "GCInvoiceNumber": "<Номер отправления клиента>",
            "PostamatNumber": "<Номер постамата для редактирования (необязательное поле)>",
            "Phone": "<Номер телефона для редактирования (необязательное поле)>",
            "RecipientName": "<Номер получателя для радактирования (необязательное поле)>",
            "Email": "<Электронный ящик получателя (необязательное поле)>",
            "Sum": "<Сумма к оплате (необязательное поле) >"
        }

        Структура ответа

        {
            "InvoiceNumber": "<Номер КО>",
            "GCInvoiceNumber": "<Номер отправления клиента>",
            "Results": "<Результаты обновления полей отправления>"
            [
                {
                    "FieldName": "<Имя поля для обновления>",
                    "Updated": "<true/false>",
                    "Comment": "<Текст ошибки>"
                }
            ]
        }

        */

        //createreturn PickPoint Регистрация возврата

    }
    public function getInfo($orderNumber='', $invoiceNumber='') { //Отслеживание статуса и информация о доставке

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //tracksending PickPoint Мониторинг отправления

        $query["SessionId"] = $this->authorization->sessionId;
        if($invoiceNumber) $query["InvoiceNumber"] = $invoiceNumber;
        else $query["SenderInvoiceNumber"] = $orderNumber;

        //print json_encode($query);

        $return = $this->authorization->query(json_encode($query), "tracksending", "POST");
        if($return['error']) return ['error' => $return['error']];
        $response['status'] = $return[0];

        /*
        Мониторинг отправления
        URL: /tracksending
        Метод: POST
        Описание
        Команда предназначена для получения статуса отправления. В запросе отправляется идентификатор сессии и номер отправления. В ответ возвращается статус отправления.
        Структура запроса
        {
            "SessionId": "<уникальный идентификатор сессии (GUID 16 байт)>",
            "InvoiceNumber": "<Номер отправления PickPoint>",
            "SenderInvoiceNumber": "<Номер заказа магазина>"
        }

        Поля InvoiceNumber и SenderInvoiceNumber являются взаимоисключающими
        Структура ответа
        [
        {
            "State": "<код статуса>",
            "ChangeDT": "<дата изменения статуса>",
            "StateMessage": "<описание статуса>"
        }
        ]

        Возможные ошибки:
        •	Нет действительной сессии с таким номером.
        •	Нет инвойса с таким номером для данного клиента.
        •	Ошибка сервера. Попробуйте повторить запрос. В случае повторения ошибки обратитесь к разработчикам.

        */


        //sendinginfo PickPoint Получение информации по отправлению

        $return = $this->authorization->query(json_encode($query), "sendinginfo", "POST");
        if($return['error']) return ['error' => $return['error']];
        $response['info'] = $return[0];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*

        Получение информации по отправлению
        URL: /sendinginfo
        Метод: POST
        Описание
        Команда предназначена для получения информации по отправлению отправления. В запросе отправляется идентификатор сессии и номер отправления. В ответ возвращается признак успешности выполнения.
        Структура запроса
        {
            "SessionId":		"<уникальный идентификатор сессии (GUID 16 байт)>",
            "InvoiceNumber":		"< Номер отправления присвоенный PickPoint (20 символов)>",
            "SenderInvoiceNumber":	"< Номер заказа в магазине (50 символов)>"
        }

        Поля InvoiceNumber и SenderInvoiceNumber являются взаимоисключающими.
        Структура ответа
        [
            {
                "InvoiceNumber": "<Номер отправления PickPoint>",
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
                            "Price": <Стоимость>,
                            "Return date": <Дата возврата (физ. лицом)>,
                            "Reason": <Причина>
                        }
                    ]

                },
                "ClientReturnAddress <Адрес клиентского возврата>": {
                    "CityName":	"<Название города>",
                    "RegionName": "<Название региона>",
                    "Address": "<Текстовое описание адреса>",
                    "FIO": "<ФИО контактного лица>",
                    "PostCode": "<Почтовый индекс>",
                    "Organisation":	"<Наименование организации>",
                    "PhoneNumber": "<Контактный телефон>",
                    "Comment": "<Комментарий>"
                },
                "UnclaimedReturnAddress <Адрес возврата невостребованного>": {
                    "CityName":	"<Название города>",
                    "RegionName": "<Название региона>",
                    "Address": "<Текстовое описание адреса>",
                    "FIO": "<ФИО контактного лица>",
                    "PostCode": "<Почтовый индекс>",
                    "Organisation":	"<Наименование организации>",
                    "PhoneNumber": "<Контактный телефон>",
                    "Comment": "<Комментарий>"
                },
                "ChequeNumber":	"<номер чека, если несколько, то перечислены через запятую>",
                "PayType": "<тип оплаты отправления cash\card>"

            }
        ]

        Замечание: в случае, если под одним SenderInvoiceNumber было зарегистрировано несколько отправлений, ответ будет содержать информацию по всем этим отправлениям.


        */

    }
    public function cancel($orderNumber='', $invoiceNumber='') { //Отмена заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //cancelInvoice PickPoint Удаление отправления

        $query["SessionId"] = $this->authorization->sessionId;
        if($invoiceNumber) $query["InvoiceNumber"] = $invoiceNumber;
        else $query["SenderInvoiceNumber"] = $orderNumber;

        $response =  $this->authorization->query(json_encode($query), "cancelInvoice", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Удаление отправления
        URL: /cancelInvoice
        Метод: POST
        Описание
        Команда предназначена для удаления ранее созданного отправления. Отправление возможно удалить только в случае, если все его вложимые находятся в состоянии 101 (зарегистрирован).
        Структура запроса
        {
            "SessionId": "<уникальный идентификатор сессии>",
            "InvoiceNumber": "<Номер КО>",
            "GCInvoiceNumber": "<Номер отправления клиента>"
        }



        Структура ответа

        {
            "Result": <true/false>,
            "Error": <Описание ошибки>
            "ErrorCode": <Код ошибки: 0 – нет ошибки, -1 - ошибка>,
        }
        */

        //removeinvoicefromreestr PickPoint Удаление отправления из реестра

    }

}
