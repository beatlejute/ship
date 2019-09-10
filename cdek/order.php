<?php

namespace cdek;

class order extends \abstracts\order {

    public function getCost($fromCity, $fromRegion='', $toCity='', $toRegion='', $toPoint='', $index='', $length='', $depth='', $width='',$weight=1, $count=1, $declaredValue=0, $invoiceNumber='') { //Расчёт стоимости доставки

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["version"] = "1.0";
        $query["dateExecute"] = date("Y-m-d");
        $query["authLogin"] = $this->authorization->authLogin;
        $query["secure"] = md5(date("Y-m-d").'&'.$this->authorization->authPassword);

        $query["senderCityId"] = $fromId;
        $query["receiverCityId"] = $toId;

        if(!$query["receiverCityId"]) $query["receiverCityPostCode"] = $index;

        $tariffList = $info->tariffList();

        foreach($tariffList as $tariffId => $tariff) {

            if((!$tariff['confines']['weight'] || floatval($weight) <= $tariff['confines']['weight']) &&
                (!$tariff['confines']['minWeight'] || floatval($weight) >= $tariff['confines']['minWeight']) &&
                ($tariff['mode']=="С-С" || $tariff['mode']=="Д-С")) {

                $query["tariffList"][$tariffId]["priority"] = $tariffId;
                $query["tariffList"][$tariffId]["id"] = $tariff['id'];

            }

        }

        $query["goods"][0]["weight"] = $weight;
        $query["goods"][0]["length"] = $length;
        $query["goods"][0]["width"] = $width;
        $query["goods"][0]["height"] = $depth;

        $request = $this->authorization->query(json_encode($query), "calculator/calculate_price_by_json.php", "POST", "json", "api");
        if($request['error']) return ['error' => $request['error']];

        foreach($tariffList as $tariffId => $tariff) {

            if($tariff['id']==$request['result']['tariffId']) {

                $tariffs[0]['name'] = $tariff['name'];
                $tariffs[0]['mode'] = $tariff['mode'];

            }

        }

        $tariffs[0]['id'] = $request['result']['tariffId'];
        $tariffs[0]['DPMin'] = $request['result']['deliveryPeriodMin'];
        $tariffs[0]['DPMax'] = $request['result']['deliveryPeriodMax'];
        $tariffs[0]['cost'] = $request['result']['price'];
        $tariffs[0]['info'] = $request;

        if(!$toPoint) {

            unset($query["tariffList"]);

            foreach($tariffList as $tariffId => $tariff) {

                if((!$tariff['confines']['weight'] || floatval($weight) <= $tariff['confines']['weight']) &&
                    (!$tariff['confines']['minWeight'] || floatval($weight) >= $tariff['confines']['minWeight']) &&
                    ($tariff['mode']=="С-Д" || $tariff['mode']=="Д-Д")) {

                    $query["tariffList"][$tariffId]["priority"] = $tariffId;
                    $query["tariffList"][$tariffId]["id"] = $tariff['id'];

                }

            }

            $request = $this->authorization->query(json_encode($query), "calculator/calculate_price_by_json.php", "POST", "json", "api");
            if($request['error']) return ['error' => $request['error']];

            foreach($tariffList as $tariffId => $tariff) {

                if($tariff['id']==$request['result']['tariffId']) {

                    $tariffs[1]['name'] = $tariff['name'];
                    $tariffs[1]['mode'] = $tariff['mode'];

                }

            }

            $tariffs[1]['id'] = $request['result']['tariffId'];
            $tariffs[1]['DPMin'] = $request['result']['deliveryPeriodMin'];
            $tariffs[1]['DPMax'] = $request['result']['deliveryPeriodMax'];
            $tariffs[1]['cost'] = $request['result']['price'];
            $tariffs[1]['info'] = $request;

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffs;

        /*
        Передаваемые на сервер параметры:
        •	version
        string, версия используемого API, например “1.0".
        •	authLogin
        string, логин (Account), выдается компанией СДЭК по вашему запросу. Обязательны для учета индивидуальных тарифов и учета условий доставок по тарифам «посылка». Запрос необходимо отправить на адрес mailto:integrator@cdek.ru с указанием номера договора со СДЭК.
        Важно: Учетная запись для интеграции не совпадает с учетной записью доступа в Личный Кабинет СДЭК.
        •	secure
        string. Получается следующим образом: md5($dateExecute."&".$authPassword) – зашифрованная md5 строка, состоящая из даты, символа амперсанда и полученного от СДЭК пароля Secure_password. Дата ($dateExecute) – планируемая дата оправки заказа в формате “ГГГГ-ММ-ДД", например “2012-07-19". $authPassword – пароль (Secure_password), выдаётся компанией СДЭК по вашему запросу.
        •	dateExecute
        string, планируемая дата оправки заказа в формате “ГГГГ-ММ-ДД", например “2012-07-19".
        •	senderCityId или senderCityPostCode
        senderCityId - integer, код города-отправителя в соответствии с кодами городов, предоставляемых компанией СДЭК (см. файл «City_XXX_YYYYMMDD.xls», где XXX – трехбуквенный код страны, YYYYMMDD – дата формирования файла)
        senderCityPostCode – integer, 6 знаков, почтовый индекс города-отправителя.
        Задаётся или senderCityId, или senderCityPostCode, при задании обоих значений берётся senderCityId, а senderCityPostCode игнорируется.
        •	receiverCityId или receiverCityPostCode
        integer, код города-получателя в соответствии с кодами городов, предоставляемых компанией СДЭК (см. файл «City_XXX_YYYYMMDD.xls», где XXX – трехбуквенный код страны, YYYYMMDD – дата формирования файла)
        receiverCityPostCode – integer, 6 знаков, почтовый индекс города-получателя.
        Задаётся или receiverCityId, или receiverCityPostCode, при задании обоих значений берётся receiverCityId, а receiverCityPostCode игнорируется.
        •	tariffId
        integer, код выбранного тарифа. Выбирается из предоставляемого СДЭК списка (см. Приложение 1).
        •	tariffList
        Array, массив с элементами, каждый из которых состоит из ассоциативного массива с ключами:
        •	priority
        integer, заданный приоритет
        •	id
        integer, код тарифа
        Список тарифов с приоритетами используется в том случае, если на выбранном направлении у СДЭК может не быть наиболее выгодного для вас, какого-то конкретного тарифа по доставке (например, на направлении Пермь (248) - Астрахань (432) нет тарифа «Посылка склад-склад» (tariffId = 136)). Т. е. тариф  «посылка» действуют не по всем направлениям и не для любого веса груза.
        В случае задания списка тарифов этот список проверяется на возможность доставки по заданному направлению с заданным весом груза последовательно (начиная с первого с наименьшим приоритетом) и проверка возможности доставки будет проходить до тех пор, пока по очередному тарифу не появится такая возможность. Тогда стоимость будет рассчитана по этому тарифу. В ответе севера будет возвращён «tariffId» из заданного списка tariffList, по которому была посчитана сумма доставки.
        •	modeId
        integer, выбранный режим доставки. Выбирается из предоставляемого СДЭК списка (см. Приложение 2).
        •	goods
        Array, массив с элементами, каждый из которых состоит из ассоциативного массива с ключами:
        •	weight
        float, вес места, кг;
        •	length
        integer, длина места, см;
        •	width
        integer, ширина места, см;
        •	height
        integer, высота места, см.
            или с такими ключами:
        •	weight
        float, вес места, кг;
        •	volume
        float, объём места, метры кубические. Данное значение будет переведено в объемный вес по формуле.

        При формировании запроса следует учесть следующее:
        •	Авторизация не обязательна. Параметры authLogin, authPassword можно не передавать. Авторизация позволяет учитывать персональные тарифы и скидки, если они у ИМ есть (персональные тарифы и скидки возможны только при заключении договора со СДЭК).
        •	Дата планируемой отправки dateExecute не обязательна (в этом случае принимается текущая дата). Но, если вы работаете с авторизацией, она должна быть обязательно передана, т. к. дата учитывается при шифровании/дешифровке пароля .
        •	При задании тарифа нужно задавать либо один выбранный тариф, либо список тарифов с приоритетами. Если задаётся и tariffId, и tariffList – принимается tariffId, а список игнорируется.
        •	Задавать режим доставки modeId имеет смысл только при задании списка тарифов. В этом случае заданный список дополнительно фильтруется по режиму доставки, в противном случае – игнорируется.
        •	Задавать места в списке можно первым вариантом (через вес, длину, ширину и высоту), вторым (через вес и объём) и комбинируя эти варианты (одно место первым, другое вторым и т.д.). Стоимость доставки будет рассчитываться исходя из наибольшего значения объёмного или физического веса.


        Пример передаваемых параметров (json-объект):
        {
            "version":"1.0",
            "dateExecute":"2012-07-27",
            "authLogin":"098f6bcd4621d373cade4e832627b4f6",
            "secure":"396fe8e7dfd37c7c9f361bba60db0874",
            "senderCityId":"270",
            "receiverCityId":"44",
            "tariffId":"137",
            "goods":
                [
                    {
                        "weight":"0.3",
                        "length":"10",
                        "width":"7",
                        "height":"5"
                    },
                    {
                        "weight":"0.1",
                        "volume":"0.1"
                    }
                ]
        }

        Возвращаемые данные в случае успеха:
        •	price
        double, сумма за доставку в рублях;
        •	deliveryPeriodMin
        integer, минимальное время доставки в днях;
        •	deliveryPeriodMax
        integer, максимальное время доставки в днях;
        •	deliveryDateMin
        string, минимальная дата доставки,  формате 'ГГГГ-ММ-ДД', например “2012-07-29";
        •	deliveryDateMax
        string, максимальная дата доставки,  формате 'ГГГГ-ММ-ДД', например “2012-07-30";
        •	tariffId
        integer, код тарифа, по которому посчитана сумма доставки;
        •	cashOnDelivery
        float, ограничение оплаты наличными, появляется только если оно есть;
        •	priceByCurrency
            float, цена в валюте, по которой интернет-магазин работает со СДЭК. Появляется в случае, если переданы authLogin и secure, по ним же и определяется валюта ИМ.
        •	currency
            string, валюта интернет-магазина (значение из справочника валют см. Приложение 4).

        Пример возвращаемых данных в случае успеха (json-объект):

        {
        result: {
            "price":"2350",
            "deliveryPeriodMin":"1",
            "deliveryPeriodMax":"2",
            "deliveryDateMin":"2012-07-28",
            "deliveryDateMax":"2012-07-29",
            "tariffId":"137" ,
            "cashOnDelivery":"30000.00"
        }
        }

        Возвращаемые данные в случае неудачи:
        •	Array. Массив ошибок (см. Приложение 3), каждый элемент которого состоит из:
        •	code
        integer, код ошибки;
        •	text
        string, текст ошибки.

        Пример возвращаемых параметров в случае неудачи (json-объект):
        {
        error: [
            {
                "code":1,
                "text":"Указанная вами версия API не поддерживается"
            }
        ]
        }
        */

    }
    public function create($putdata) { $orders = $putdata; //Создание заказа на доставку

        $info = new info($this->authorization, $this->memcache);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //print_r($orders);

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<DeliveryRequest Number="'.time().'" Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'" OrderCount="'.sizeof($orders).'">';

        foreach($orders as $orderId => $order) {

            $from = $info->cityList($order["Pickup"]["fromCity"]);
            $SendCityCode = $from[0]["Id"];

            unset($RecCityCode);

            if($order["Address"]["city"]) {

                $to = $info->cityList($order["Address"]["city"]);
                $RecCityCode = $to[0]["Id"];

            } elseif($order["toPoint"]) {

                $point = $info->parcelShopsList('', '', '', '', '', $order["toPoint"]);
                $to = $info->cityList($point[0]["CitiName"]);
                $RecCityCode = $to[0]["Id"];

            }

            if($order["payOnDelivery"]) {
                foreach($order["Places"] as $placeId => $place) {
                    foreach($place["SubEncloses"] as $itemId => $item) {
                        $order["declaredValue"] -= $item['Price']*$item["count"];
                    }
                }
            } else $order["declaredValue"] = 0;
            if($order["declaredValue"]<0) $order["declaredValue"] = 0;

            $xml .= '	<Order 
								Number="'.$order["orderNumber"].'" 
								SendCityCode="'.$SendCityCode.'" 
								RecCityCode="'.$RecCityCode.'" 
								RecCityPostCode="'.$order["Address"]["index"].'" 
								RecipientName="'.$order["recipient"]["name"].'" 
								'.($order["recipient"]["email"] ? 'RecipientEmail="'.$order["recipient"]["email"].'"' : '').' 
								Phone="'.$order["recipient"]["phone"].'" 
								TariffTypeCode="'.$order["tariff"].'" 
								DeliveryRecipientCost="'.$order["declaredValue"].'" 
								RecientCurrency="RUB"
								ItemsCurrency="RUB" 
								Comment="'.preg_replace('"', ';', $order["description"]).'">
								<Address
									'.($order["Address"]["street"] ? 'Street="'.$order["Address"]["street"].'"' : 'Street="-"').'
									'.($order["Address"]["building"] ? 'House="'.$order["Address"]["building"].($order["Address"]["Housing"] ? '/'.$order["Address"]["Housing"] : '').'"' : '').'
									'.($order["Address"]["Apartment"] ? 'Flat="'.$order["Address"]["Apartment"].'"' : '').'
									'.($order["toPoint"] ? 'PvzCode="'.$order["toPoint"].'"' : '').' /> ';

            foreach($order["Places"] as $placeId => $place) {

                $xml .= '		<Package 
									Number="'.$placeId.'" 
									BarCode="'.$placeId.'" 
									Weight="'.($place["Weight"]*1000).'"
									'/*.($place["Depth"] ? 'SizeA="'.$place["Depth"].'"' : '').'
									'.($place["Width"] ? 'SizeB="'.$place["Width"].'"' : '').'
									'.($place["Height"] ? 'SizeC="'.$place["Height"].'"' : '')*/.' 
									> 	';

                foreach($place["SubEncloses"] as $itemId => $item) {

                    $xml .= '				<Item 
											WareKey="'.$item['GoodsCode'].'" 
											Cost="'.$item['Price'].'" 
											Payment="'.($order["payOnDelivery"] ? ($item['Price']) : 0).'" 
											Weight="'.($item["Weight"]*1000).'" 
											Amount="'.$item["count"].'" 
											Comment="'.preg_replace('"', '', $item["name"]).'"/> 	 ';

                }

                $xml .= '		</Package>  ';

            }

            $xml .= '	</Order> 	
				';

        }

        $xml .= '	</DeliveryRequest>';

        $request = array(
            'xml_request' => $xml
        );


        $orderList = $this->authorization->query($request, "new_orders.php", "POST", "xml");
        if($orderList['error']) return ['error' => $orderList['error']];


        if($orderList['DeliveryRequest']) {

            $response['RejectedSendings']['ErrorCode'] = $orderList['DeliveryRequest']['ErrorCode'];
            $response['RejectedSendings']['ErrorMessage'] = $orderList['DeliveryRequest']['@attributes']['Msg'];

        }

        foreach($orderList['Order'] as $orderId => $order) {

            if($order['@attributes']['ErrorCode']) {

                $response['RejectedSendings'][$orderId]['ErrorCode'] = $order['@attributes']['ErrorCode'];
                $response['RejectedSendings'][$orderId]['ErrorMessage'] = $order['@attributes']['Msg'];
                $response['RejectedSendings'][$orderId]['orderNumber'] = $order['@attributes']['Number'];

            } else {

                $response['CreatedSendings'][$orderId]['InvoiceNumber'] = $order['@attributes']['DispatchNumber'];
                $response['CreatedSendings'][$orderId]['orderNumber'] = $order['@attributes']['Number'];

            }

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Список заказов на доставку
        Для использования необходимо отправить POST запрос на URL: <сервер>/new_orders.php, например, https://integration.cdek.ru/new_orders.php с заполненной переменной $_POST['xml_request'], в которой передается содержимое XML фaйла.
        Описание передаваемых данных:
        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	DeliveryRequest	Заголовок документа		да
        1.1.	Number 	Номер акта приема-передачи/ТТН (сопроводительного документа при передаче груза СДЭК, формируется в системе ИМ), так же используется для удаления заказов.  Идентификатор реестра грузов в ИС клиента СДЭК. По умолчанию можно использовать 1.	string(30)	да
        1.2.	Date	Дата документа (дата заказа)	Date time/date	да
        1.3.	Account	Идентификатор ИМ, передаваемый СДЭКом.	string(255)	да
        1.4.	Secure	Ключ (см. п.1.4)
        string(255)	да
        1.5.	OrderCount	Общее количество заказов в документе, по умолчанию 1.	integer	да
        1.6.	 Order	Отправление (заказ)		да
        1.6.1.	Number	Номер отправления клиента (должен быть уникален в пределах акта приема-передачи). Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        1.6.2.	  SendCityCode  2	Код города отправителя из базы СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        1.6.3.	  RecCityCode  2	Код города получателя из базы СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        1.6.4.	  SendCityPostCode  2	Почтовый индекс города отправителя	string(6)	да
        1.6.5.	  RecCityPostCode  2	Почтовый индекс города получателя	string(6)	да
        1.6.6.	  RecipientName	Получатель (ФИО)	string(128)	да
        1.6.7.	  RecipientEmail	Email получателя для рассылки уведомлений о движении заказа, для связи в случае недозвона. 	еmail	нет
        1.6.8.	  Phone	Телефон получателя	phone	да
        1.6.9.	  TariffTypeCode	Код типа тарифа (см. Приложение, таблица 1)
        integer	да
        1.6.10.	  DeliveryRecipientCost 1	Доп. сбор за доставку, которую ИМ берет с получателя (в указанной валюте)	float	нет
        1.6.11.	  RecipientCurrency  7	Код валюты наложенного платежа: доп. сбора за доставку и  оплата за товар, которые надо взять с получателя (см. Приложение, таблица 7). Валюта считается как валюта страны получателя.	string (10)	нет
        1.6.12.	  ItemsCurrency	Код валюты объявленной стоимости заказа (всех вложений) (см. Приложение, таблица 7). Валюта взаиморасчетов с клиентом СДЭК по договору.	string(10)	нет
        1.6.13.
        DeliveryRecipientVATRate   9	Ставка НДС РФ (может содержать значения
        VATX - БЕЗ НДС, VAT0 - 0%, VAT10 - 10%, VAT18 - 18%). В зависимости от этого значения рассчитывается сумма НДС за доп. сбор за доставку, которую ИМ берет с получателя (DeliveryRecipientCost* )string(255)	string(255)	нет
        1.6.14.
        DeliveryRecipientVATSum 9	Сумма НДС за единицу товара (рассчитывается как выделение ставки НДС из доп. сбора за доставку, которую ИМ берет с получателя (DeliveryRecipientCost* ). Округление значения до двух знаков после запятой	float	нет
        1.6.15.	  Comment  8	Комментарий особые отметки по заказу	string(255)	нет
        1.6.16.	  SellerName	Истинный продавец. Используется при печати заказов для отображения настоящего продавца товара, либо торгового названия	string(255)	нет
        1.6.15.	  Address	Адрес доставки. В зависимости от режима доставки необходимо указывать либо атрибуты «Street», «House», «Flat» - доставка до адресата получателя, либо «PvzCode» - самозабор		да
        1.6.15.1	   Street	Улица получателя. Рекомендуем по возможности не указывать префиксы значений, вроде «ул.»	string(50)	да
        1.6.15.2	   House	Дом, корпус, строение получателя.  Рекомендуем по возможности не указывать префиксы значений, вроде «дом»	string(30)	да
        1.6.15.3	   Flat	Квартира/Офис получателя. Рекомендуем по возможности не указывать префиксы значений, вроде «кв.»	string(10)	нет
        1.6.15.4	   PvzCode	Код ПВЗ (см. «Список пунктов выдачи заказов (ПВЗ)»). Атрибут необходим только для заказов с режимом доставки «до склада» и при условии, что не заказана дополнительная услуга "Доставка в городе получателе"(AddService="17").
        Коды ПВЗ с Type="POSTOMAT" ownerCode="InPost доступны, только если выбрана услуга InPost и Китайский экспресс (см. Приложение, таблица 1).
        Если указанный ПВЗ в момент создания заказа закрыт, то заказ будет принят на другой открытый ПВЗ, находящийся рядом с выбранным. Получателю при этом уйдет СМС оповещение о замене ПВЗ. Если в городе все ПВЗ в соответствии с выбранной услугой закрыты, то регистрация заказа невозможна.	string(10)	да
        1.6.16.	  Package	Упаковка (все упаковки передаются в разных тэгах Package)		да
        1.6.16.1	   Number 	Номер упаковки (можно использовать порядковый номер упаковки заказа или номер заказа), уникален в пределах заказа. Идентификатор заказа в ИС клиента СДЭК.	string(20)	да
        1.6.16.2	   BarCode	Штрих-код упаковки, идентификатор грузоместа (если есть, иначе передавать значение номера упаковки Packege.Number). Параметр используется для оперирования грузом на складах СДЭК), уникален в пределах заказа. Идентификатор грузоместа в ИС клиента СДЭК.	string(20)	да
        1.6.16.3	   Weight	Общий вес (в граммах)	integer	да
        1.6.16.4	   SizeA  4	Габариты упаковки. Длина (в сантиметрах)	integer	нет
        1.6.16.5	   SizeB  4	Габариты упаковки. Ширина (в сантиметрах)	integer	нет
        1.6.16.6	   SizeC  4	Габариты упаковки. Высота (в сантиметрах)	integer	нет
        1.6.16.7	   Item 	Вложение (товар)		да
        1.6.16.7.1	    WareKey	Идентификатор/артикул товара/вложения (Уникален в пределах упаковки Package).	string(20)	да
        1.6.16.7.2	    Cost	Объявленная стоимость товара (за единицу товара в указанной валюте, значение >=0). С данного значения рассчитывается страховка.	float	да
        1.6.16.7.3	    Payment	Оплата за товар при получении (за единицу товара в указанной валюте, значение >=0) — наложенный платеж, в случае предоплаты значение = 0.	float	да
        1.6.16.7.4	PaymentVATRate 10	Ставка НДС РФ (может содержать значения
        VATX - БЕЗ НДС, VAT0 - 0%, VAT10 - 10%, VAT18 - 18%). В зависимости от данного значения рассчитывается сумма НДС за единицу товара	string(255)	нет
        1.6.16.7.5	PaymentVATSum 10	Сумма НДС за единицу товара (рассчитывается как выделение ставки НДС из оплаты за товар при получении за единицу (Payment)	float	нет
        1.6.16.7.6	    Weight	Вес (за единицу товара, в граммах)	integer	да
        1.6.16.7.7	    Amount	Количество единиц товара (в штуках)	integer	да
        1.6.16.7.8	    Comment	Наименование товара (может также содержать описание товара: размер, цвет)	string(255)	да
        1.6.17.	  AddService  6	Дополнительные услуги		нет
        1.6.17.1	   ServiceCode 	Тип дополнительной услуги (см. Приложение, таблица 5).
        integer	да
        1.6.18	  Schedule  3	Расписание времени доставки		нет
        1.6.18.1	   Attempt	Время доставки. В один день возможен один временной интервал не менее 3 часов.		да
        1.6.18.1.1	    ID	Идентификационный номер расписания по базе ИМ. По умолчанию можно использовать 1	string(20)	да
        1.6.18.1.2	    Date	Дата доставки (только дата, в формате «YYYY-MM-DD», без времени) согласованная с получателем	date	да
        1.6.18.1.3	    TimeBeg	Начало временного диапазона доставки (время получателя)	time	да
        1.6.18.1.4	    TimeEnd	Окончание временного диапазона доставки (время получателя)	time	да
        1.6.18.1.5	    RecipientName	Новый Получатель (если требуется изменить)	string(128)	нет
        1.6.18.1.6	    Phone	Новый номер телефона получателя (если требуется изменить)	phone	нет
        1.6.18.1.7	    Address	Новый адрес доставки (если требуется изменить). В зависимости от режима доставки необходимо указывать либо атрибуты «Street», «House», «Flat» - доставка до адресата получателя, либо «PvzCode» - самозабор		нет
        1.6.18.1.7.1	     Street	Улица получателя. Рекомендуем по возможности не указывать префиксы значений, вроде «ул.»	string(50)	да
        1.6.18.1.7.2	     House	Дом, корпус, строение получателя.  Рекомендуем по возможности не указывать префиксы значений, вроде «дом»	string(30)	да
        1.6.18.1.7.3	     Flat	Квартира/Офис получателя.  Рекомендуем по возможности не указывать префиксы значений, вроде «кв.»	string(10)	нет
        1.6.18.1.7.4	     PvzCode	Код ПВЗ (см. «Список пунктов выдачи заказов (ПВЗ)»). Атрибут необходим только для заказов с режимом доставки «до склада» и при условии, что не заказана дополнительная услуга "Доставка в городе получателе"(AddService="17")	string(10)	да
        1.6.18.1.7.5	     Comment	Комментарий	string(255)	нет
        1.7.1	  CallCourier 5	Вызов курьера		нет
        1.7.1.1	   Call	Приезд курьера		да
        1.7.1.1.1	    Date	Дата ожидания курьера	date	да
        1.7.1.1.2	    TimeBeg	Время начала ожидания курьера	time	да
        1.7.1.1.3	    TimeEnd	Время окончания ожидания курьера	time	да
        1.7.1.1.4	    LunchBeg	Время начала обеда, если входит во временной диапазон [TimeBeg; TimeEnd]	time	нет
        1.7.1.1.5	    LunchEnd	Время окончания обеда, если входит во временной диапазон [TimeBeg; TimeEnd]	time	нет
        1.7.1.1.6	    SendCityCode	Код города отправителя из базы СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        1.7.1.1.7	    SendPhone	Контактный телефон отправителя	phone	да
        1.7.1.1.8	    Comment	Комментарий для курьера	string(255)	нет
        1.7.1.1.9	    SenderName	Отправитель (ФИО)	string(255)	да
        1.7.1.1.10	    SendAddress	Адрес отправителя		да
        1.7.1.1.10.1	     Street	Улица отправителя. Рекомендуем по возможности не указывать префиксы значений, вроде «ул.»	string(50)	да
        1.7.1.1.10.2	     House	Дом, корпус, строение отправителя.  Рекомендуем по возможности не указывать префиксы значений, вроде «дом»	string(30)	да
        1.7.1.1.10.3	     Flat	Квартира/Офис отправителя.  Рекомендуем по возможности не указывать префиксы значений, вроде «кв.»	string(10)	да

        1 В случае, если услуги доставки СДЭК оплачивает не получатель, а ИМ, в стоимость заказа может быть включена стоимость доставки, которую ИМ берет с получателя, например, в качестве компенсации своих расходов. Эта сумма может отличаться от стоимости доставки по тарифам СДЭК. Значение параметра отображается в квитанции к заказу в поле «Стоимость доставки», но при этом входит в сумму наложенного платежа и обрабатывается как наложенный платеж.
        2 Идентификация города возможна двумя способами на выбор:
        •	По уникальному коду города базы СДЭК (ID города). Коды городов базы СДЭК можно найти в реестрах городов в пакете документации (см. файл «City_XXX_YYYYMMDD.xls»). Значения передаются в атрибутах SendCityCode, RecCityCode.
        •	По почтовому индексу города. Значения передаются в атрибутах  SendCityPostCode,  RecCityPostCode.
        Если указан и атрибут Код города и Почтовый индекс, то приоритет для определения города имеет Код города, это относится к определению, как города отправителя, так и города получателя.
        Чаще всего город отправления фиксируют кодом города, а для определения города  получателя используется почтовый индекс.
        Нужно принимать во внимание, что база почтовых индекс в ИС СДЭК может, содержат неполную и иногда не точную информацию по почтовым индексам, поэтому пользователь должен иметь возможность скорректировать параметр почтового индекса, если это необходимо.
        Примечание: На данный момент ИС СДЭК содержит почтовые индексы только России. Для идентификации городов других стран рекомендуем использовать код города по БД СДЭК.
        3 В договоре с ИМ определяется условие кто именно, ИМ или СДЭК, запрашивает у получателя расписание для доставки/забора отправления. В случае, если ИМ самостоятельно запрашивает расписание данные передаются в тэге Schedule. Расписание может быть передано позже, при необходимости (см. документ «Прозвон получателя»). На одну дату по одному заказу может быть только одно расписание. Расписание может иметь несколько дней доставки.
        4 Габариты упаковки необходимо указывать, если упаковка представляет собой коробку. С учетом габаритов  вычисляется объемный вес по формуле SizeА * SizeВ * SizeС/5000. Расчет стоимости доставки идет из максимального значения между фактическим и объемным весом.
        5 Вызов курьера осуществляется один на акт передачи и не более одного вызова курьера в день на один адрес.
        6 Для добавления заказа доступны не все доп. услуги из списка (см. Приложение, таблицу 5). В таблице 5 приведены все доп. услуги, которые могут быть в конечном варианте заказа (запрашивать конечное состояние можно при переходе заказа в конечный статус).
        7 Для доставки грузов на территории Казахстана наложено дополнительное ограничение. Валюта наложенного платежа должна совпадать с валютой договора (валютой взаиморасчетов) иначе будет сообщение об ошибке ErrorCode="ERR_CURRCASH_NOTVALID" Msg="Валюта наложенного платежа должна совпадать с валютой договора".
        8 В поле комментарий можно писать любые примечания по доставке груза, например если вы разрешаете частичную выдачу груза можно указывать “Частичная доставка разрешена», если запрещаете вскрытие посылки можно указывать «Запрет вскрытия».
        9 Поля используются при указании города получателя в Российской Федерации и Доп. сбор за доставку>0
        10 Поля используются при указании города получателя в Российской Федерации и наложенного платежа>0
        Сервер СДЭК вернет результат в виде:
        •	при удачной обработке данных header("http/1.0 200 Ok");
                и XML в виде:
                <?xml version="1.0" encoding="UTF-8"?>
                <response>
                 <Order Number="Номер заказа" DispatchNumber ="Номер накладной СДЭК"/>
                <Order Msg="Добавлено заказов CntOrder"/>
                </response>
        •	при ошибке: header("http/1.0 200 server error")
                и XML в виде:
                <?xml version="1.0" encoding="UTF-8"?>
                <response>< Order Number="Номер заказа" ErrorCode="Код ошибки» Msg="Error: описание ошибки"/></response>


        Описание получаемых данных:
        Параметр	Описание	Обязательность
        Number 	Номер заказа. Идентификатор заказа в ИС ИМ.	да
        DispatchNumber	Номер накладной СДЭК. Идентификатор заказа в ИС СДЭК.	нет
        CntOrder 	Количество принятых заказов	нет
        Msg	Текст сообщения. 	нет
        ErrorCode	Код ошибки.
        Весь список ошибок см. в ErrorCode.xls	нет

         Файл содержит данные по двум заказам.
        Первый заказ: Используется тариф до склада СДЭК (ПВЗ), осуществляется самозабор получателем. В заказе указана только сумма сколько взять за  сами товары, за доставку с получателя ничего не взымается.
        Второй заказ: Используется тарифа до двери получателя — осуществляется курьерская доставка. В заказе указана сумма сколько взять с получателя за доставку, т.  е. данная сумма будет суммироваться как часть наложенного платежа, при этом она не обязана быть равна сумме за доставку, которую СДЭК выставляет в счете самому ИМ. В заказе не указана стоимость сколько взять с получателя, т.  е. считается что товар или бесплатный или была произведена предоплата продавцу.
        <?xml version="1.0" encoding="UTF-8" ?>
        <DeliveryRequest Number="236" Date="2010-10-14" Account="abc123" Secure="abcd1234" OrderCount="2">
            <Order Number="5403"
            DeliveryRecipientCost="0"
            SendCityCode="270"
            RecCityCode="44"
            RecipientName="Васина Юлия Александровна"
            Phone="7810999, 9295849151"
            Comment="Офис группы компаний Ланит. При приезде позвонить на мобильный телефон."
            TariffTypeCode="5"
            RecientCurrency="RUB"
            ItemsCurrency="RUB">
            <Address PvzCode="MSK2" />
            <Package Number="1" BarCode="101" Weight="630">
               <Item WareKey="25000050368" Cost="49" Payment="49" Weight="68" Amount="1" Comment="Дидактические игры-занятия в ДОУ 	 	   Ст.возраст Вып. 1"/>
               <Item WareKey="25000348563" Cost="79" Payment="79" Weight="95" Amount="1" Comment="ДошкВоспитаниеИРазвитие(Айрис-Пр.)	(о) 	   Сюжетно-роле"/>
               <Item WareKey="25000373314" Cost="79" Payment="79" Weight="135" Amount="1" Comment="ДошкВоспитаниеИРазвитие(Айрис-Пр.)	(о) 	   Метод.работа"/>
               <Item WareKey="25000390270" Cost="79" Payment="79" Weight="219" Amount="1" Comment="Дошкольники_УчимРазвиваемВоспитываем 	   Родительские "/>
            </Package>
            <AddService ServiceCode="30"></AddService>
            <Schedule>
               <Attempt ID="1" Date="2010-10-15" TimeBeg="09:00:00" TimeEnd="13:00:00" />
               <Attempt ID="2" Date="2010-10-16" TimeBeg="14:00:00" TimeEnd="18:00:00" RecipientName="Прокопьев 	Анатолий Сергеевич" />
            </Schedule>
            </Order>
            <Order Number="5404"
            DeliveryRecipientCost="150"
            SendCityCode="270"
            RecCityCode="44"
            RecipientName="Lubomir Dmitry Vladimirovich"
            Phone="9197747341"
            SellerName="Ruston"
            RecientCurrency="RUB"
            ItemsCurrency="RUB"
            Comment="Офис группы компаний Ланит. При приезде позвонить на мобильный телефон."
            TariffTypeCode="11">
            <Address Street="Боровая" House="д. 7, стр. 2" Flat="оф.10" />
            <Package Number="1" BarCode="102" Weight="810">
               <Item WareKey="25000358171" Cost="164" Payment="0" Weight="158" Amount="1" Comment="ХочуУчиться Логика (Беденко 		М.В.)"/>
               <Item WareKey="25000428787" Cost="107" Payment="0" Weight="194" Amount="1" Comment="ЛомоносовскаяШкола(о) Считаю и 	решаю Д/детей 5-6 л"/>
               <Item WareKey="33000002164" Cost="107"  Payment="0" Weight="174" Amount="1" Comment="ЛомоносовскаяШкола(о) Говорю 	красиво Д/детей 6-7 л"/>
               <Item WareKey="33000002165" Cost="107" Payment="0" Weight="174" Amount="1" Comment="ЛомоносовскаяШкола(о) Говорю 	красиво Д/детей 6-7 л"/>
            </Package>
            <Package Number="2" BarCode="103" Weight="740">
               <Item WareKey="25000086458" Cost="427" Payment="0" Weight="323" Amount="2" Comment="Перемены Рук-во к личной 	трансформации и новые спо"/>
               <Item WareKey="25000377899" Cost="238" Payment="0" Weight="310" Amount="1" Comment="Коэльо П.(АСТ)(тв)(цв.) Вероника 	решает умереть"/>
             </Package>
            <AddService ServiceCode="29"></AddService>
            <AddService ServiceCode="30"></AddService>
            <Schedule>
               <Attempt ID="3" Date="2010-10-15" TimeBeg="19:00:00" TimeEnd="22:00:00"/>
            </Schedule>
            </Order>
        </DeliveryRequest>

         Файл содержит данные по одновременному созданию заказа и заявки на вызов курьера.
        В данном примере курьер вызывается для забора груза в городе Новосибирске на 15.10.2016, с временем ожидания 11:00 до 18:00 (по местному времени), на адрес: Военная дом 6 кв 9.

        <?xml version="1.0" encoding="UTF-8" ?>
        <DeliveryRequest Number="237" Date="2016-10-13" Account="abc123" Secure="qwe456" OrderCount="1">
        <Order Number="653" DeliveryRecipientCost="150" SendCityCode="270"
        RecCityCode="44" RecipientName="Тест1"
        Phone="9191111341" 	TariffTypeCode="138" 	SellerName="Иванов ИИ" 	RecientCurrency="RUB" 	ItemsCurrency="RUB" 	Comment="test">
        <Address PvzCode="MSK2" />
            <Package Number="1" BarCode="101" Weight="10">
                <Item WareKey="111" Cost="0" Payment="0" Weight="10" Amount="1" Comment="cd"/>
            </Package>
        </Order>
        <CallCourier>
            <Call  Date="2016-10-15"  TimeBeg="11:00:00" TimeEnd="18:00:00"	SendCityCode="270" SenderName="Иванов Иван И">
                <SendAddress  Street="Воeнная" House="6"  Flat="9" />
            </Call>
        </CallCourier>
        </DeliveryRequest>

           Пример создания заказа с указанием ставки НДС по каждому товару.

        <DeliveryRequest Number="1413" Date="2017-06-05" Account="111" Secure="222" OrderCount="1">
        <Order Number="43566"
        Comment="Доставка оплачивается в любом случае"
         DeliveryRecipientCost="0"
         SendCityCode="137"
         RecCityCode="521"
         RecipientName=" Алексей "
         Phone="9233065625"
         TariffTypeCode="137"
         SellerName="трейд"
         RecientCurrency="RUB"
         ItemsCurrency="RUB">
         Phone="92330000625"
                 AddService="37"
         TariffTypeCode="137">
         <Address Street="Карбышева" House="19" Flat="" />
         <Package Number="1" BarCode="102" Weight="450"><Item WareKey="373843580" Cost="1000" Payment="6990"
         Weight="450" Amount="1" Comment="450"/><Item
                           WareKey="7992863_1"
                           Cost="1999.00"
                           PaymentVATRate="VAT10"
                           PaymentVATSum="181.73"
                           Payment="1999.00"
                           Weight="960.00"
                           Amount="1"
                           Comment="Ползунки детские 3 в уп"/>
                     <Item
                           WareKey="8534468_1"
                           Cost="999.00"
                           PaymentVATRate="VAT10"
                           PaymentVATSum="90.82"
                           Payment="999.00"
                           Weight="960.00"
                           Amount="1"
                    Comment="Ползунки детские 3 в уп"/></Package>
                <AddService ServiceCode="37"></AddService>
            </Order>
         </DeliveryRequest>
        */

    }
    public function edit($orderNumber, $invoiceNumber='', $recipientName='', $recipientPhone='', $recipientEmail='', $declaredValue='', $toPoint='', $addressIndex='', $addressCountry='', $addressRegion='', $addressCity='', $addressStreet='', $addressBuilding='', $addressHousing='', $addressApartment='', $addressPorch='', $addressFloor='', $addressInfo='') { //Изменение заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<ScheduleRequest Number="1" Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'" OrderCount="1">';

        //foreach($orders as $orderId => $order) {

            $xml .= '	<Order 
								'.($invoiceNumber ? 'DispatchNumber="'.$invoiceNumber.'"' : '').'
								'.($orderNumber ? 'Number="'.$orderNumber.'"' : '').'
								Date=""
								> 	
								<Attempt
									ID="1"
									Date=""
									'.($recipientName ? 'RecipientName="'.$recipientName.'"' : '').'
									'.($recipientPhone ? 'Phone="'.$recipientPhone.'"' : '').'
									'.($addressInfo ? 'Comment="'.$addressInfo.'"' : '').'
									> ';
            if($addressStreet || $addressBuilding || $addressApartment || $toPoint)
                $xml .= '			<Address 
										'.($addressStreet ? 'Street="'.$addressStreet.'"' : '').'
										'.($addressBuilding ? 'House="'.$addressBuilding.($addressHousing ? '/'.$addressHousing : '').'"' : '').'
										'.($addressApartment ? 'Flat="'.$addressApartment.'"' : '').'
										'.($toPoint ? 'PvzCode="'.$toPoint.'"' : '').' />';
            $xml .= '		</Attempt>
							</Order> 	
				';

        //}

        $xml .= '	</ScheduleRequest>';

        $request = array(
            'xml_request' => $xml
        );

        $request = $this->authorization->query($request, "new_schedule.php", "POST", "xml");
        if($request['error']) return ['error' => $request['error']];


        $order = $request['Order'];

        $response["InvoiceNumber"] = $order['@attributes']['DispatchNumber'];
        $response["GCInvoiceNumber"] = $order['@attributes']['Number'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Прозвон получателя
        Для использования необходимо отправить POST запрос на URL: <сервер>/new_schedule.php, например,  https://integration.cdek.ru/new_schedule.php с заполненной переменной $_POST['xml_request'], в которой передается содержимое XML фaйла.
        Описание передаваемых данных:
        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	ScheduleRequest	Заголовок документа		да
        1.1.	Date	Дата документа	date time/date	да
        1.2.	Account	Идентификатор ИМ, передаваемый СДЭКом.	string(255)	да
        1.3.	Secure	Ключ (см. п.1.4.)
        string(255)	да
        1.4.	OrderCount	Общее количество заказов в документе	integer	да
        2.	 Order	Отправление (заказ)		да
        2.1.	  DispatchNumber  1	Номер отправления СДЭК (присваивается при импорте заказов). Идентификатор заказа в ИС СДЭК.	integer	да
        2.2.	  Number  1	Номер отправления клиента. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        2.3.	  Date  1	Дата акта приема-передачи, в котором был передан заказ	date	да
        2.4	  Attempt	Попытка доставки		да
        2.4.1	   ID	Идентификационный номер попытки по базе ИМ. Умолчанию можно использовать 1.	integer	да
        2.4.2	   Date	Дата доставки (только дата, в формате «YYYY-MM-DD», без времени)	date	да
        2.4.3	   TimeBeg  2	Временной интервал доставки. Начало (время получателя)	time	нет
        2.4.4	   TimeEnd  2	Временной интервал доставки. Окончание (время получателя)	time	нет
        2.4.5	   RecipientName	Новый получатель	string(128)	нет
        2.4.6	   Phone	Новый номер телефона получателя	phone	нет
        2.4.7	   DeliveryRecipientCost	Новый доп. сбор за доставку, которую ИМ берет с получателя	float	нет
        2.4.8	   Comment	Комментарий для доставки	string(255)	нет
        2.4.9	   Address	Новый адрес доставки. В зависимости от режима доставки необходимо указывать либо атрибуты «Street», «House», «Flat» - доставка до адресата получателя, либо «PvzCode» - самозабор		нет
        2.4.9.1	    Street	Улица получателя. Рекомендуем по возможности не указывать префиксы значений, вроде «ул.»	string(50)	да
        2.4.9.2	    House	Дом, корпус, строение получателя.  Рекомендуем по возможности не указывать префиксы значений, вроде «дом»	string(30)	да
        2.4.9.3	    Flat	Квартира/Офис получателя.  Рекомендуем по возможности не указывать префиксы значений, вроде «кв.»	string(10)	да
        2.4.9.4	    PvzCode	Код ПВЗ (см. «Список пунктов выдачи заказов (ПВЗ)»). Атрибут необходим только для заказов с режим доставки «до склада» и при условии, что не заказана дополнительная услуга "Доставка в городе получателе"(AddService="17")	string(10)	да
        2.4.9.5	     Package	Упаковка		нет
        2.4.9.5.1	     Number	Штрих-код упаковки, идентификатор грузоместа (значение параметра DeliveryRequest.Order.Package.BarCode. если есть, иначе передавать значение номера упаковки DeliveryRequest.Order.Package.Number). Параметр используется для оперирования грузом на складах СДЭК, уникален в пределах заказа. Идентификатор грузоместа в ИС клиента СДЭК.	string(20)	да
        2.4.9.5.2	   BarCode	Штрих-код упаковки, идентификатор грузоместа (если есть, иначе передавать значение номера упаковки Packege.Number). Параметр используется для оперирования грузом на складах СДЭК), уникален в пределах заказа. Идентификатор грузоместа в ИС клиента СДЭК.	string(20)	да
        2.4.9.5.3	     Item 	Вложение		       да
        2.4.9.5.3.1	      WareKey	Идентификатор/артикул товара/вложения (Уникален в пределах упаковки Package).	string(20)	да
        2.4.9.5.3.2	      Payment	Оплата за товар/вложение при получении (за единицу).	float	да

        1 Идентификация заказа осуществляется либо по «DispatchNumber», либо по двум параметрам «Number», «Date». Если в запросе есть значение атрибута «DispatchNumber», то атрибуты «Number», «Date» игнорируются.
        2 Временной интервал является обязательным в режиме доставки «до двери» для передачи информации курьеру.
         Файл содержит дополнительное расписание для заказа 5403, при этом сменился получатель и данные по нему; для заказа 5404 сменилась сумма  оплаты товара при получении (при передаче заказа, сумма была равна стоимости товара Item.Cost)
        <?xml version="1.0" encoding="UTF-8" ?>
        <ScheduleRequest Date="2010-10-14" Account="abc123" Secure="abcd1234" OrderCount="2">
            <Order Number="5403" Date="2010-10-14">
            <Attempt ID="4" Date="2010-10-16" TimeBeg="09:00:00" TimeEnd="13:00:00" 	RecipientName="Зимина Юлия 	Владимировна"
            Phone="79296071468">
            <Address Street="Просторная" House="д.9"  Flat="оф.10"  />
            </Attempt>
            </Order>
            <Order Number="5404" Date="2010-10-14T00:00:00">
            <Attempt ID="5" Date="2010-10-16" >
            <Package Number="1">
            <Item WareKey="25000428787" Payment="50"/>
            </Package>
            </Attempt>
            </Order>
        </ScheduleRequest>
        */

    }
    public function getInfo($orderNumber='', $invoiceNumber='') { //Отслеживание статуса и информация о доставке

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<StatusReport Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'">
							<Order 
								'.($invoiceNumber ? 'DispatchNumber="'.$invoiceNumber.'"' : '').'
								'.($orderNumber ? 'Number="'.$orderNumber.'"' : '').'
								Date=""
								/>
						</StatusReport>';

        $request = array(
            'xml_request' => $xml
        );

        $requeststatus = $this->authorization->query($request, "status_report_h.php", "POST", "xml");
        if($requeststatus['error']) return ['error' => $requeststatus['error']];

        /*
        {
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
            ]
        }
        */


        $order = $requeststatus['Order'];

        $response['status']['State'] = $order['Status']['@attributes']['Code'];
        $response['status']['ChangeDT'] =  date("d.m.Y H:i:s", strtotime($order['Status']['@attributes']['Date']));
        $response['status']['StateMessage'] = $order['Status']['@attributes']['Description'];

        $response['info']['InvoiceNumber'] = $order['@attributes']['DispatchNumber'];
        $response['info']['SenderInvoiceNumber'] = $order['@attributes']['Number'];
        $response['info']['FIO'] = $order['@attributes']['RecipientName'];

        /*
        <?xml version="1.0" encoding="UTF-8" ?>
          <StatusReport DateFirst="2013-07-16T00:00:00">
            <Order ActNumber ="236"
            Number ="6346860"
            DispatchNumber ="1001013928"
            DeliveryDate="2013-07-16T14:23:00"
            RecipientName="Иванов И.">
                <Status Date="2013-07-17T00:00:00"
            Code="4"
            Description="Вручен" CityCode="270">
                <State Date="2013-07-16T08:12:00" Code="8" Description="Отправлен в г.-получатель" CityCode="44" />
                <State Date="2013-07-16T09:40:00" Code="10" Description="Принят на склад доставки" CityCode="270" />
                <State Date="2013-07-16T14:23:00" Code="4" Description="Вручен" CityCode="270" />
            </Status>
            <Reason Date="2013-07-16T14:23:00" Code="20" Description="Частичная доставка" />
            <Package Number="1">
                <Item WareKey="25000050368" DelivAmount="1"/>
                <Item WareKey="25000348563" DelivAmount="1"/>
            </Package>
           </Order>
           <Order ActNumber="236"
            Number="6346869"
            DispatchNumber="1001013929" >
            <Status Date="2013-07-16T18:40:00"
            Code="11"
            Description="Возвращен на склад доставки" CityCode="44">
                <State Date="2013-07-16T08:10:00" Code="10" Description="Принят на склад доставки" CityCode="44" />
                <State Date="2013-07-16T08:23:00" Code="11" Description="Выдан на доставку" CityCode="44" />
                <State Date="2013-07-16T18:40:00" Code="18" Description="Возвращен на склад доставки" CityCode="44" />
            </Status>
            <DelayReason  Date="2013-07-16T18:40:00" Code="12" DelayDescription="Контактное лицо отсутствует">
            <Attempt ID="1" ScheduleCode="4" ScheduleDescription="Перенос. Контактное лицо отсутствует"/>
           </Order>
         </StatusReport>
        */

        if(!$invoiceNumber) $invoiceNumber = $response['info']['InvoiceNumber'];

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<InfoRequest Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'">
							<Order 
								'.($invoiceNumber ? 'DispatchNumber="'.$invoiceNumber.'"' : '').'
								'.($orderNumber ? 'Number="'.$orderNumber.'"' : '').'
								Date=""
								/>
						</InfoRequest>';

        $request = array(
            'xml_request' => $xml
        );

        /*<?xml version="1.0" encoding="UTF-8"?><response><Order Number="НФ-226134" Date="" ErrorCode="ERR_INVALID_NUMBER" Msg="заказ не найден в базе СДЭК: Number=НФ-226134, Date="/></response>*/

        $requestinfo = $this->authorization->query($request, "info_report.php", "POST", "xml");
        if(isset($requestinfo['error'])) return ['error' => $requestinfo['error']];

        //print_r($requestinfo);

        $order = $requestinfo['Order'];

        $response['info']['Sum'] = $order['@attributes']['DeliverySum'];
        $response['info']['CreateDate'] = date("d.m.Y H:i:s", strtotime($order['@attributes']['Date']));
        $response['info']['StorageDate'] = '';
        $response['info']['Prolonged'] = false;
        $response['info']['PayType'] = ($order['@attributes']['CashOnDelivType'] == 'cashless') ? 'card' : $order['@attributes']['CashOnDelivType'];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

    }
    public function cancel($orderNumber='', $invoiceNumber='') { //Отмена заказа

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        /*if(!$orderNumber) {

            $status = $this->authorization->query("{}", "getStatus?number=".$orderNumber."&json=1", "GET");
            $query[] = $status[0]->id;

        }*/

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<DeleteRequest Number="'.time().'" Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'" OrderCount="1">
							<Order Number="'.$orderNumber.'" />
						</DeleteRequest>';

        $request = array(
            'xml_request' => $xml
        );

        /*
        <?xml version="1.0" encoding="UTF-8"?>
        <response>
            <DeleteRequest Msg="Удалено заказов:1" >
                <Order Number="НФ-1234" Msg="Заказ удален" />
            </DeleteRequest>
        </response>
        */

        $requestcancel = $this->authorization->query($request, "delete_orders.php", "POST", "xml");
        if($requestcancel['error']) return ['error' => $requestcancel['error']];

        $order = $requestcancel['DeleteRequest']['Order'];

        if($order['@attributes']['Msg'] == "Заказ удален") {
            $response["Result"] = true;
            $response["Error"] = "";
            $response["ErrorCode"] = 0;
        } else {
            $response["Result"] = false;
            $response["Error"] = $order['@attributes']['Msg'];
            $response["ErrorCode"] = 1;
        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Список заказов на удаление
        Для использования необходимо отправить POST запрос на URL: <сервер>/delete_orders.php, например,  https://integration.cdek.ru/delete_orders.php с заполненной переменной $_POST['xml_request'], в которой передается содержимое XML фaйла.

        Вы сможете удалить информацию о посылке из системы компании СДЭК, только если она находится в статусе «Создан». Во всех остальных статусах данная операция (удаления) будет не возможна!
        Описание передаваемых данных:
        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	DeleteRequest	Заголовок документа		да
        1.1.	  Number	Номер акта приема-передачи. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        1.2.	 Date	Дата документа (дата заказа)	datetime/date	да
        1.3.	 Account	Идентификатор ИМ, передаваемый СДЭКом.	string(255)	да
        1.4.	 Secure	Ключ (см. п. 1.4.)
        string(255)	да
        1.5.	 OrderCount	Общее количество заказов для удаления в документе. По умолчанию 1.	integer	да
        2.	 Order	Отправление (заказ)		да
        2.1.	  Number 	Номер отправления клиента. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да

         Документ содержит данные для удаления двух заказов.
        <?xml version="1.0" encoding="UTF-8" ?>
        <DeleteRequest Number="236" Date="2010-10-14" Account="abc123" Secure="abcd1234" OrderCount="2">
            <Order Number="5403" />
            <Order Number="5404" />
        </DeleteRequest>

        */

    }

}
