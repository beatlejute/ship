<?php

namespace dpd;

class info extends \abstracts\info {

    public function methodsList() { //Получение списка методов

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $status[0]['method'] = "info/methodsList";
        $status[0]['active'] = true;
        $status[1]['method'] = "info/cityList";
        $status[1]['active'] = true;
        $status[2]['method'] = "info/parcelShopsList";
        $status[2]['active'] = true;
        $status[3]['method'] = "info/tariffList";
        $status[3]['active'] = true;
        $status[4]['method'] = "info/statusList";
        $status[4]['active'] = true;
        $status[5]['method'] = "info/errorList";
        $status[5]['active'] = true;
        $status[6]['method'] = "order/getCost";
        $status[6]['active'] = true;
        $status[7]['method'] = "order/create";
        $status[7]['active'] = true;
        $status[8]['method'] = "order/getInfo";
        $status[8]['active'] = true;
        $status[9]['method'] = "order/edit";
        $status[9]['active'] = false;
        $status[10]['method'] = "order/cancel";
        $status[10]['active'] = true;
        $status[11]['method'] = "batches/getOrderList";
        $status[11]['active'] = false;
        $status[12]['method'] = "batches/getLabel";
        $status[12]['active'] = false;
        $status[13]['method'] = "batches/create";
        $status[13]['active'] = false;
        $status[14]['method'] = "batches/getInfo";
        $status[14]['active'] = false;
        $status[15]['method'] = "batches/removeOrder";
        $status[15]['active'] = false;
        $status[16]['method'] = "allocates/courier";
        $status[16]['active'] = false;
        $status[17]['method'] = "allocates/cancel";
        $status[17]['active'] = false;
        $status[18]['method'] = "returns/create";
        $status[18]['active'] = false;
        $status[19]['method'] = "returns/getReturnsList";
        $status[19]['active'] = false;
        $status[20]['method'] = "returns/getInfo";
        $status[20]['active'] = false;


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $status;
    }
    public function cityList($cityName='', $regionName='', $kladr='') {

        $namespace = __NAMESPACE__;

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(empty($citylist)) {

            $query['countryCode'] = 'RU';

            $response = $this->authorization->query($query, "geography2?wsdl", "getCitiesCashPay", 'request');
            if(isset($response['error'])) return ['error' => $response['error']];

            if(!$response) {

                $answer['error']['code'] = "_1";
                $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

                return $answer;

            }
            if(!is_array($response)) {

                $answer['error']['code'] = "_2";
                $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                $answer['error']['info'][] = print_r($response, true);

                return $answer;

            }

            foreach($response as $cityId => $city) {

                if($city['cityName']) $citylist[$cityId]['name'] = $city['cityName'];
                if($city['cityId']) $citylist[$cityId]['Id'] = $city['cityId'];
                if($city['regionName']) $citylist[$cityId]['RegionName'] = $city['regionName'] . ' обл.';
                if($city['regionCode']) $citylist[$cityId]['RegionId'] = $city['regionCode'];
                if($city['cityCode']) $citylist[$cityId]['kladr'] = $city['cityCode'];

            }


            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $citylist, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $citylist;

        /*
          1.5.1.	Параметры входного сообщения getCitiesCashPay
            Параметр	                    Описание	            Тип	        Обязательный	        Пример
            Внешний тэг	    request
            auth
                            clientNumber	Ваш клиентский номер в  Число	    Да	                    1000000000
                            clientKey	    Ваш уникальный ключ для
                                            авторизации, полученный
                                            у сотрудника DPD        Строка	    Да	                    1FD890C3556
            countryCode		                Код страны	            Строка	    Нет	                    RU

        ПРИМЕЧАНИЯ. Поля countryCode  задаются для фильтрации списка. Если поле не задано, возвращается полный список населенных пунктов зоны RU.

          1.5.2.	Параметры ответа getCitiesCashPay
            Параметр	                    Описание	            Тип	        Пример
            city		                    Массив городов с
                                            поддержкой доставки с
                                            наложенным платежом
                            cityId	        Идентификатор города 	Число	    195644235
                            countryCode	    Код страны 	            Строка	    RU
                            countryName	    Страна	                Строка	    Россия
                            regionCode	    Код региона	            Число	    50
                            regionName	    Регион	                Строка	    Московская
                            cityCode	    Код населенного пункта	Строка	    50017001000
                            cityName	    Населенный пункт	    Строка	    Люберцы (буквенные обозначения аббревиатур и других знаков)
                            abbreviation	Аббревиатура 	        Строка	    г
                            indexMin	    Минимальный индекс	    Строка 	    140000
                            indexMax	    Максимальный индекс	    Строка 	    143818

         */
    }
    public function parcelShopsList($cityName='', $regionName='', $weight=0, $size=0, $payment='', $number=''){//Получение списка точек самовывоза

        $namespace = __NAMESPACE__;

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(empty($parcelShopsList)) {

            $query['countryCode'] = 'RU';

            $response = $this->authorization->query($query, "geography2?wsdl", "getParcelShops", 'request');
            if(isset($response['error']))  return ['error' => $response['error']];
            if($response && $response['parcelShop']) $response = $response['parcelShop'];

            $response2 = $this->authorization->query([], "geography2?wsdl", "getTerminalsSelfDelivery2");
            if(isset($response2['error'])) return ['error' => $response2['error']];
            if($response2 && $response2['terminal']) $response2 = $response2['terminal'];
            if($response2 && $response2['parcelShop']) $response2 = [];

            if(is_array($response) && is_array($response2)) $response = array_merge($response, $response2);

            if(!$response) {

                $answer['error']['code'] = "_1";
                $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

                return $answer;

            }
            if(!is_array($response)) {

                $answer['error']['code'] = "_2";
                $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                $answer['error']['info'][] = print_r($response, true);

                return $answer;

            }

            foreach($response as $pvzId => $pvz) {

                if($pvz['code']) $parcelShopsList[$pvzId]['Id'] = $pvz['code'];
                if($pvz['code']) $parcelShopsList[$pvzId]['Number'] = $pvz['code'];
                if($pvz['terminalCode']) $parcelShopsList[$pvzId]['Id'] = $pvz['terminalCode'];
                if($pvz['terminalCode']) $parcelShopsList[$pvzId]['Number'] = $pvz['terminalCode'];
                if($pvz['brand']) $parcelShopsList[$pvzId]['OwnerName'] = $pvz['brand'];
                if($pvz['parcelShopType']) switch ($pvz['parcelShopType']) {
                    case 'П':
                        $parcelShopsList[$pvzId]['TypeTitle'] = 'АПТ';
                        break;
                    case 'ПВП':
                        $parcelShopsList[$pvzId]['TypeTitle'] = 'ПВЗ';
                        break;
                }
                if($pvz['state']) switch ($pvz['state']) {
                    case 'open':
                        $parcelShopsList[$pvzId]['Status'] = 2;
                        break;
                    case 'full':
                        $parcelShopsList[$pvzId]['Status'] = 3;
                        break;
                }
                if($pvz['address']['cityId']) $parcelShopsList[$pvzId]['CitiId'] = $pvz['address']['cityId'];
                if($pvz['address']['regionName']) $parcelShopsList[$pvzId]['Region'] = $pvz['address']['regionName'] . ' обл.';
                $parcelShopsList[$pvzId]["Region"] = str_replace(' обл. обл.', ' обл.', $parcelShopsList[$pvzId]["Region"]);
                if($pvz['address']['cityName']) $parcelShopsList[$pvzId]['CitiName'] = $pvz['address']['cityName'];
                if($pvz['address']['street']) $parcelShopsList[$pvzId]['Street'] = $pvz['address']['street'];
                if($pvz['address']['houseNo']) $parcelShopsList[$pvzId]['House'] = $pvz['address']['houseNo'];
                if($pvz['address']['building']) $parcelShopsList[$pvzId]['BuildingType'] = $pvz['address']['building'].' ';
                if($pvz['address']['structure']) $parcelShopsList[$pvzId]['BuildingType'] .= $pvz['address']['structure'];
                if($pvz['address']['index']) $parcelShopsList[$pvzId]['PostCode'] .= $pvz['address']['index'];
                if($pvz['address']['descript']) $parcelShopsList[$pvzId]['OutDescription'] = $pvz['address']['descript'];
                if($pvz['geoCoordinates']['latitude']) $parcelShopsList[$pvzId]['Latitude'] = $pvz['geoCoordinates']['latitude'];
                if($pvz['geoCoordinates']['longitude']) $parcelShopsList[$pvzId]['Longitude'] = $pvz['geoCoordinates']['longitude'];
                $parcelShopsList[$pvzId]['MaxWeight'] = $pvz['limits']['maxWeight'] ?: 10;
                $parcelShopsList[$pvzId]['MaxSize'] = $pvz['limits']['dimensionSum'] ?: 100;
                if($pvz['terminalCode']){
                    $parcelShopsList[$pvzId]['MaxWeight'] = 250;
                    $parcelShopsList[$pvzId]['MaxSize'] = 300;
                }
                if(!$parcelShopsList[$pvzId]['MaxSize']) $parcelShopsList[$pvzId]['MaxSize'] = 100;
                if($pvz['schedule']['timetable']['weekDays']) $parcelShopsList[$pvzId]['WorkTime'] = $pvz['schedule']['timetable']['weekDays'].' ';
                if($pvz['schedule']['timetable']['workTime']) $parcelShopsList[$pvzId]['WorkTime'] .= $pvz['schedule']['timetable']['workTime'];
                $parcelShopsList[$pvzId]['Cash'] = true;
                $parcelShopsList[$pvzId]['Card'] = false;
                if($pvz['schedule']['operation']) switch ($pvz['schedule']['operation']) {
                    case 'Payment':
                        $parcelShopsList[$pvzId]['Card'] = false;
                        break;
                    case 'PaymentByBankCard':
                        $parcelShopsList[$pvzId]['Card'] = true;
                        break;
                }
                $parcelShopsList[$pvzId]["Address"] = $parcelShopsList[$pvzId]['CitiName'].', '.$parcelShopsList[$pvzId]['Street'].', '.$parcelShopsList[$pvzId]['House'].' '.(array_key_exists('BuildingType', $parcelShopsList[$pvzId]) ? $parcelShopsList[$pvzId]['BuildingType'] : '');


            }


            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $parcelShopsList, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $parcelShopsListReturn;

        /*
         1.5.3.	Параметры входного сообщения getParcelShops

            Параметр	                    Описание	            Тип	        Обязательный	        Пример
            Внешний тэг	    request
            auth
                            clientNumber	Ваш клиентский номер в
                                            системе DPD (номер
                                            вашего договора с DPD)	Число	    Да	                    1000000000
                            clientKey	    Ваш уникальный ключ
                                            для авторизации,
                                            полученный у сотрудника
                                            DPD	                    Строка	    Да	                    1FD890C3556
            countryCode		                Код страны 	            Строка	    Нет	                    RU
            regionCode		                Код региона 	        Число 	    Нет	                    77
            cityCode		                Код  города     	    Строка	    Нет	                    77011000011
            cityName		                Наименование города	    Строка	    Нет	                    Москва (буквенные обозначения аббревиатур и других знаков)

            ПРИМЕЧАНИЯ. 1.  Поля countryCode, regionCode и cityCode задаются для фильтрации списка. Если коды не заданы, возвращается полный список. Поле cityName  анализируется только, если не задан cityCode.
            2. Сроки бесплатного хранения зависят от услуги DPD. Для получения сроков бесплатного хранения необходимо задать поле serviceCode.


        1.5.6.	Параметры выходного сообщения  getParcelShops

            Параметр		                Описание	            Тип	        Пример
            parcelShop			            Массив ПВП и пунктов
                            code		    Код подразделения DPD	Строка	    LED
                            parcelShopType	Тип подразделения.      Строка	    П
                            state		    Состояние подразделения Строка	    Open
                            address		    Адрес пункта	        address

                            geoCoordinates	Географические
                                            координаты по
                                            карте Яндекс	        geoCoordinates

                            limits		    Ограничения
                                            параметров посылки	    limits

                            schedule		Массив операций
                                            производственного
                                            подразделения	        schedule

                            extraService	Массив опций	        extraService

                            services		Массив услуг	        services

                            brand

        1.7.	Параметры входного сообщения getTerminalsSelfDelivery2

            Параметр	                    Описание	            Тип	        Обязательный	        Пример
            Внешний тэг	    request
            auth
                            clientNumber	Ваш клиентский номер в
                                            системе DPD (номер
                                            вашего договора с DPD)	Число	    Да	                    1000000000
                            clientKey	    Ваш уникальный ключ
                                            для авторизации,
                                            полученный у сотрудника
                                            DPD	                    Строка	    Да	                    1FD890C3556

        1.7.1.	Параметры выходного сообщения  getTerminalsSelfDelivery2

            Параметр	                    Описание	            Тип	                Пример
            terminal		                Массив терминалов DPD
	                        terminalCode	Код терминала DPD	    Строка	            M13
	                        terminalName	Название терминала DPD	Строка	            Москва
	                        address	        Адрес пункта	        address

	                        geoCoordinates	Географические
                                            координаты по карте
                                            Яндекс	                geoCoordinates

	                        schedule	    Массив операций
                                            производственного
                                            подразделения	        schedule

	                        extraService	Массив опций	        extraService

	                        services	    Массив услуг	        services

            1.5.7.	Описание типа address

            Параметр	                    Описание	            Тип	                    Пример
            cityId	                        Идентификатор города
                                            отправлени	            Число	                195868771
            countryCode	                    Код страны 	            Строка	                RU
            regionCode	                    Код региона	            Число	                77
            regionName	                    Регион	                Строка	                Московская обл. (формат ФИАС)
            cityCode	                    Код города	            Строка	                77000000000
            cityName	                    Город 	                Строка	                Москва (без буквенных обозначений аббревиатур и других знаков)
            street	                        Наименование улицы	    Строка	                Земляной Вал
            streetAbbr	                    Аббревиатура улицы	    Строка	                ул
            houseNo	                        Номер дома	            Строка	                7
            building	                    Корпус	                Строка
            structure	                    Строение	            Строка
            ownership	                    Владение	            Строка
            descript	                    Описание проезда	    Строка

            1.5.8.	Описание типа geoCoordinates

            Параметр	                    Описание	            Тип	                    Пример
            latitude	                    Широта	                Число	                53.322927
            longitude	                    Долгота	                Число	                83.638803

            1.5.9.	Описание типа limits

            Параметр	                    Описание	            Тип	                    Пример
            maxShipmentWeight	            Макс. вес отправки в кг	Число	                30.5
            maxWeight	                    Макс. Вес посылки  в кг	Число	                5.5
            maxLength	                    Макс. длина в см	    Число	                70
            maxWidth	                    Макс. ширина в см	    Число	                70
            maxHeight	                    Макс. высота в см	    Число	                50
            dimensionSum	                Сумма габаритов в см	Число	                200

            1.5.10.	Описание типа schedule

            Параметр	                    Описание	            Тип	                    Пример
            operation	                    Наименование операции   Строка	                SelfDelivery
            timetable	                    Массив расписания
                                            для операции	        timetable

            1.5.11.	Описание типа timetable

            Параметр	                    Описание	            Тип	                    Пример
            weekDays	                    Список дней недели	    Строка	                пн,вт,ср,чт,пт
            workTime	                    Список времени работы	Строка	                09:00–20:00

            1.5.12.	Описание типа terminal

            Параметр	                    Описание	            Тип	                    Пример

            terminal		                Массив  подразделений
                                            с указанием срока
                                            хранения
                            terminalCоde	Код пункта
                                            приема/выдачи	        Строка	                41W
                            services	    Массив услуг            service

            1.5.13.	Описание типа service

            Параметр	                    Описание	            Тип	                    Пример
            serviceCode	                    Код услуги	            Строка	                PCL
            days	                        Количество дней	        Число	                3

            1.6.	Описание атрибутов ответного сообщения getParcelShops
            1.6.1.	Список возможных типов подразделения
            •	П – постамат
            •	ПВП – пункт приема/выдачи посылок
            1.6.2.	Список возможный состояний пункта
            •	open (Открыт)
            •	full (Переполнен)
            1.6.3.	Возможные варианты операций
            •	SelfPickup – прием посылок
            •	SelfDelivery – выдача посылок
            •	Payment - оплата наличными за доставку
            •	PaymentByBankCard - оплата банковской картой
            1.6.4.	Описание типа extraService
            Параметр	Описание	Тип	Пример
            esCode	Код опции. См описания опций в разделе «Опции».
            Строка	НПП

         */
    }
    public function shippingPointsList(){
    }
    public function tariffList(){

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $tariffList[0]['id'] = "ECN";
        $tariffList[0]['name'] = "DPD ECONOMY";
        $tariffList[0]['mode'] = "Д-Д";
        $tariffList[0]['confines']['weight'] = "1000";
        $tariffList[0]['confines']['length'] = "350";
        $tariffList[0]['confines']['depth'] = "160";
        $tariffList[0]['confines']['width'] = "180";
        $tariffList[0]['type'] = "Классическая доставка";
        $tariffList[0]['description'] = "Экономичная доставка тяжелых посылок и сборных грузов по территории России.";

        $tariffList[1]['id'] = "BZP";
        $tariffList[1]['name'] = "DPD 18:00";
        $tariffList[1]['mode'] = "Д-Д";
        $tariffList[1]['confines']['weight'] = "800";
        $tariffList[1]['confines']['length'] = "150";
        $tariffList[1]['confines']['depth'] = "170";
        $tariffList[1]['confines']['width'] = "120";
        $tariffList[1]['type'] = "Экспресс-доставка";
        $tariffList[1]['description'] = "Оптимальное решение для срочной доставки посылок и грузов к 18:00 часам определенного дня.";

        $tariffList[2]['id'] = "CUR";
        $tariffList[2]['name'] = "DPD CLASSIC domestic";
        $tariffList[2]['mode'] = "Д-Д";
        $tariffList[2]['confines']['weight'] = "400";
        $tariffList[2]['confines']['length'] = "120";
        $tariffList[2]['confines']['depth'] = "80";
        $tariffList[2]['confines']['width'] = "80";
        $tariffList[2]['type'] = "Классическая доставка";
        $tariffList[2]['description'] = "Надежная доставка документов и посылок по всей России.";

        $tariffList[3]['id'] = "NDY";
        $tariffList[3]['name'] = "DPD EXPRESS";
        $tariffList[3]['mode'] = "Д-Д";
        $tariffList[3]['confines']['weight'] = "150";
        $tariffList[3]['confines']['length'] = "70";
        $tariffList[3]['confines']['depth'] = "70";
        $tariffList[3]['confines']['width'] = "70";
        $tariffList[3]['type'] = "Экспресс-доставка";
        $tariffList[3]['description'] = "Экспресс-доставка документов и посылок по всей России.";

        $tariffList[4]['id'] = "CSM";
        $tariffList[4]['name'] = "DPD Online Express";
        $tariffList[4]['mode'] = "Д-Д";
        $tariffList[4]['confines']['weight'] = "150";
        $tariffList[4]['confines']['length'] = "70";
        $tariffList[4]['confines']['depth'] = "70";
        $tariffList[4]['confines']['width'] = "70";
        $tariffList[4]['type'] = "Экспресс-доставка";
        $tariffList[4]['description'] = "Экспресс-доставка документов и посылок по всей России.";

        $tariffList[5]['id'] = "PCL";
        $tariffList[5]['name'] = "Доставка до двери";
        $tariffList[5]['mode'] = "Д-Д";
        $tariffList[5]['confines']['weight'] = "400";
        $tariffList[5]['confines']['length'] = "120";
        $tariffList[5]['confines']['depth'] = "80";
        $tariffList[5]['confines']['width'] = "80";
        $tariffList[5]['type'] = "Классическая доставка";
        $tariffList[5]['description'] = "Классическая доставка авиа и автотранспортом по всей стране";

        $tariffList[6]['id'] = "MAX";
        $tariffList[6]['name'] = "DPD MAX domestic";
        $tariffList[6]['mode'] = "Д-Д";
        $tariffList[6]['confines']['weight'] = "1000";
        $tariffList[6]['confines']['length'] = "350";
        $tariffList[6]['confines']['depth'] = "160";
        $tariffList[6]['confines']['width'] = "180";
        $tariffList[6]['type'] = "Доставка тяжелых грузов";
        $tariffList[6]['description'] = "Экономичная доставка тяжелых грузов по территории России и странам ТС";

        $tariffList[7]['id'] = "MXO";
        $tariffList[7]['name'] = "DPD Online Max";
        $tariffList[7]['mode'] = "Д-Д";
        $tariffList[7]['confines']['weight'] = "1000";
        $tariffList[7]['confines']['length'] = "1000";
        $tariffList[7]['confines']['depth'] = "1000";
        $tariffList[7]['confines']['width'] = "1000";
        $tariffList[7]['type'] = "Доставка крупногабаритных посылок";
        $tariffList[7]['description'] = "Доставка крупногабаритных посылок по России с услугой подъема на этаж";

        $tariffList[8]['id'] = "PKT";
        $tariffList[8]['name'] = "DPD Packet";
        $tariffList[8]['mode'] = "Д-Д";
        $tariffList[8]['confines']['weight'] = "30";
        $tariffList[8]['confines']['length'] = "30";
        $tariffList[8]['confines']['depth'] = "30";
        $tariffList[8]['confines']['width'] = "30";
        $tariffList[8]['type'] = "Доставка писем и небольших посылок";
        $tariffList[8]['description'] = "Быстрая и выгодная доставка писем и небольших посылок с оплатой наличными/банковской картой";


        $tariffList_p[0]['id'] = "ECN_P";
        $tariffList_p[0]['name'] = "DPD ECONOMY";
        $tariffList_p[0]['mode'] = "Д-С";
        $tariffList_p[0]['confines']['weight'] = "1000";
        $tariffList_p[0]['confines']['length'] = "350";
        $tariffList_p[0]['confines']['depth'] = "160";
        $tariffList_p[0]['confines']['width'] = "180";
        $tariffList_p[0]['type'] = "Классическая доставка";
        $tariffList_p[0]['description'] = "Экономичная доставка тяжелых посылок и сборных грузов по территории России до ПВЗ.";

        $tariffList_p[1]['id'] = "BZP_P";
        $tariffList_p[1]['name'] = "DPD 18:00";
        $tariffList_p[1]['mode'] = "Д-С";
        $tariffList_p[1]['confines']['weight'] = "800";
        $tariffList_p[1]['confines']['length'] = "150";
        $tariffList_p[1]['confines']['depth'] = "170";
        $tariffList_p[1]['confines']['width'] = "120";
        $tariffList_p[1]['type'] = "Экспресс-доставка";
        $tariffList_p[1]['description'] = "Оптимальное решение для срочной доставки посылок и грузов к 18:00 часам определенного дня до ПВЗ.";

        $tariffList_p[2]['id'] = "CUR_P";
        $tariffList_p[2]['name'] = "DPD CLASSIC domestic";
        $tariffList_p[2]['mode'] = "Д-С";
        $tariffList_p[2]['confines']['weight'] = "400";
        $tariffList_p[2]['confines']['length'] = "120";
        $tariffList_p[2]['confines']['depth'] = "80";
        $tariffList_p[2]['confines']['width'] = "80";
        $tariffList_p[2]['type'] = "Классическая доставка";
        $tariffList_p[2]['description'] = "Надежная доставка документов и посылок по всей России до ПВЗ.";

        $tariffList_p[3]['id'] = "NDY_P";
        $tariffList_p[3]['name'] = "DPD EXPRESS";
        $tariffList_p[3]['mode'] = "Д-С";
        $tariffList_p[3]['confines']['weight'] = "150";
        $tariffList_p[3]['confines']['length'] = "70";
        $tariffList_p[3]['confines']['depth'] = "70";
        $tariffList_p[3]['confines']['width'] = "70";
        $tariffList_p[3]['type'] = "Экспресс-доставка";
        $tariffList_p[3]['description'] = "Экспресс-доставка документов и посылок по всей России до ПВЗ.";

        $tariffList_p[4]['id'] = "CSM_P";
        $tariffList_p[4]['name'] = "DPD Online Express";
        $tariffList_p[4]['mode'] = "Д-С";
        $tariffList_p[4]['confines']['weight'] = "150";
        $tariffList_p[4]['confines']['length'] = "70";
        $tariffList_p[4]['confines']['depth'] = "70";
        $tariffList_p[4]['confines']['width'] = "70";
        $tariffList_p[4]['type'] = "Экспресс-доставка";
        $tariffList_p[4]['description'] = "Экспресс-доставка документов и посылок по всей России до ПВЗ.";

        $tariffList_p[5]['id'] = "PCL_P";
        $tariffList_p[5]['name'] = "Доставка до ПВЗ";
        $tariffList_p[5]['mode'] = "Д-С";
        $tariffList_p[5]['confines']['weight'] = "400";
        $tariffList_p[5]['confines']['length'] = "120";
        $tariffList_p[5]['confines']['depth'] = "80";
        $tariffList_p[5]['confines']['width'] = "80";
        $tariffList_p[5]['type'] = "Классическая доставка";
        $tariffList_p[5]['description'] = "Классическая доставка авиа и автотранспортом по всей стране до ПВЗ.";

        $tariffList_p[6]['id'] = "MAX_P";
        $tariffList_p[6]['name'] = "DPD MAX domestic";
        $tariffList_p[6]['mode'] = "Д-С";
        $tariffList_p[6]['confines']['weight'] = "1000";
        $tariffList_p[6]['confines']['length'] = "350";
        $tariffList_p[6]['confines']['depth'] = "160";
        $tariffList_p[6]['confines']['width'] = "180";
        $tariffList_p[6]['type'] = "Доставка тяжелых грузов";
        $tariffList_p[6]['description'] = "Экономичная доставка тяжелых грузов по территории России и странам ТС до ПВЗ.";

        $tariffList_p[7]['id'] = "MXO_P";
        $tariffList_p[7]['name'] = "DPD Online Max";
        $tariffList_p[7]['mode'] = "Д-С";
        $tariffList_p[7]['confines']['weight'] = "1000";
        $tariffList_p[7]['confines']['length'] = "1000";
        $tariffList_p[7]['confines']['depth'] = "1000";
        $tariffList_p[7]['confines']['width'] = "1000";
        $tariffList_p[7]['type'] = "Доставка крупногабаритных посылок";
        $tariffList_p[7]['description'] = "Доставка крупногабаритных посылок по России с услугой подъема на этаж до ПВЗ.";

        $tariffList[8]['id'] = "PKT_P";
        $tariffList[8]['name'] = "DPD Packet";
        $tariffList[8]['mode'] = "Д-С";
        $tariffList[8]['confines']['weight'] = "30";
        $tariffList[8]['confines']['length'] = "30";
        $tariffList[8]['confines']['depth'] = "30";
        $tariffList[8]['confines']['width'] = "30";
        $tariffList[8]['type'] = "Доставка писем и небольших посылок";
        $tariffList[8]['description'] = "Быстрая и выгодная доставка писем и небольших посылок с оплатой наличными/банковской картой";

        $tariffList = array_merge($tariffList, $tariffList_p);
        $tariffList = array_values($tariffList);


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffList;

    }
    public function errorList(){

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $errors[0]['Error'] = 'system-error';
        $errors[0]['ErrorText'] = 'Системная ошибка';

        $errors[1]['Error'] = 'too-many-calls';
        $errors[1]['ErrorText'] = 'Превышен лимит одновременных вызовов сервиса';

        $errors[2]['Error'] = 'call-client-twin';
        $errors[2]['ErrorText'] = 'Повторный вызов сервиса по одному коду клиента';

        $errors[3]['Error'] = 'client-num-error';
        $errors[3]['ErrorText'] = 'Не найден номер клиента';

        $errors[4]['Error'] = 'auth-error';
        $errors[4]['ErrorText'] = 'Ошибка в параметрах аутентификации';

        $errors[5]['Error'] = 'required-value';
        $errors[5]['ErrorText'] = 'Не заданы значения обязательных полей';

        $errors[6]['Error'] = 'error-value';
        $errors[6]['ErrorText'] = 'Недопустимое значение поля';

        $errors[7]['Error'] = 'access-denied';
        $errors[7]['ErrorText'] = 'Превышен суточный лимит вызовов сервиса клиентом';

        $errors[8]['Error'] = 'no-terminalCоde-found';
        $errors[8]['ErrorText'] = 'Не удалось найти указанный код подразделения <Код подразделения DPD>';

        $errors[9]['Error'] = 'no-serviceCode-found';
        $errors[9]['ErrorText'] = 'Не удалось найти указанный код услуги <Код услуги>';

        $errors[10]['Error'] = 'no-data-found';
        $errors[10]['ErrorText'] = 'Нет данных для  указанных  <Код подразделения DPD>, <Код услуги> ';

        $errors[11]['Error'] = 'required-value';
        $errors[11]['ErrorText'] = 'Не заданы значения обязательных полей';

        $errors[12]['Error'] = 'no-service-available';
        $errors[12]['ErrorText'] = 'Невозможна услуга, удовлетворяющая запросу';

        $errors[13]['Error'] = 'no-data-found';
        $errors[13]['ErrorText'] = 'Данные не найдены';

        $errors[14]['Error'] = 'too-many-rows';
        $errors[14]['ErrorText'] = 'Найдено более одной записи данных';

        $errors[15]['Error'] = 'no-setting';
        $errors[15]['ErrorText'] = 'Клиент не подписан на Web сервис Event Tracking. / Клиент не подписан на Web сервис Parcel Tracing.';

        $errors[16]['Error'] = 'date-before-start';
        $errors[16]['ErrorText'] = 'Дата запрошенного объекта ранее подключения клиента к сервису';

        $errors[17]['Error'] = 'confirm-not-required';
        $errors[17]['ErrorText'] = 'Запрос не предполагает подтверждения';

        $errors[18]['Error'] = 'code-already-exists';
        $errors[18]['ErrorText'] = 'Адрес с указанным кодом уже существует';

        $errors[19]['Error'] = 'address-error';
        $errors[19]['ErrorText'] = 'Адрес не может быть создан.';

        $errors[20]['Error'] = 'code-not-found';
        $errors[20]['ErrorText'] = 'Адрес с указанным кодом не найден.';

        $errors[21]['Error'] = 'input-data-error';
        $errors[21]['ErrorText'] = 'У заказа № дата забора ранее текущей даты';

        $errors[22]['Error'] = 'illegal-state';
        $errors[22]['ErrorText'] = 'Состояние заказа № не позволяет создавать наклейки';


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $errors;
    }
    public function statusList(){

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $statusList[0]['State'] = "NewOrderByDPD";
        $statusList[0]['StateText'] = "Оформлен новый заказ по инициативе DPD";

        $statusList[1]['State'] = "NewOrderByClient";
        $statusList[1]['StateText'] = "оформлен новый заказ по инициативе клиента";

        $statusList[2]['State'] = "NotDone";
        $statusList[2]['StateText'] = "Заказ отменен ";

        $statusList[3]['State'] = "OnTerminalPickup";
        $statusList[3]['StateText'] = "Посылка находится на терминале приема отправления";

        $statusList[4]['State'] = "OnRoad";
        $statusList[4]['StateText'] = "Посылка находится в пути (внутренняя перевозка DPD)";

        $statusList[5]['State'] = "OnTerminal";
        $statusList[5]['StateText'] = "Посылка находится на транзитном терминале";

        $statusList[6]['State'] = "OnTerminalDelivery";
        $statusList[6]['StateText'] = "Посылка находится на терминале доставки";

        $statusList[7]['State'] = "Delivering";
        $statusList[7]['StateText'] = "Посылка выведена на доставку";

        $statusList[8]['State'] = "Delivered";
        $statusList[8]['StateText'] = "Посылка доставлена получателю";

        $statusList[9]['State'] = "Lost";
        $statusList[9]['StateText'] = "Посылка утеряна";

        $statusList[10]['State'] = "Problem";
        $statusList[10]['StateText'] = "С посылкой возникла проблемная ситуация";

        $statusList[11]['State'] = "ReturnedFromDelivery";
        $statusList[11]['StateText'] = "Посылка возвращена с доставки";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $statusList;
    }
}
