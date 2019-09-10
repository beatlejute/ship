<?php

namespace easyway;

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
        $status[11]['active'] = true;
        $status[12]['method'] = "batches/getLabel";
        $status[12]['active'] = true;
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
        $status[19]['active'] = true;
        $status[20]['method'] = "returns/getInfo";
        $status[20]['active'] = false;


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $status;

    }
    public function cityList($cityName='', $regionName='', $kladr='') { //Получение списка городов

        $namespace = __NAMESPACE__;

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(empty($citylist)) {


            $citylist = $this->authorization->query("{}", "getPickupPoints", "GET");
            if(isset($citylist['error'])) return ['error' => $citylist['error']];

            if(!$citylist) {

                $answer['error']['code'] = "_1";
                $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

                return $answer;

            }
            if(!is_array($citylist)) {

                $answer['error']['code'] = "_2";
                $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                $answer['error']['info'][] = print_r($citylist, true);

                return $answer;

            }

            foreach($citylist as $cityId => $city) if(!$citysExist[$id = md5($city['city'])]) {

                $citysExist[$id] = true;

                $citylist[$cityId]['Id'] = md5($city['city']);
                $citylist[$cityId]['name'] = $city['city'];
                if(array_key_exists('RegionName', $city)) {
                    $citylist[$cityId]['RegionName'] = $city['RegionName'];
                    $citylist[$cityId]['RegionId'] = $city['kladr'];
                } else {

                    $region = $this->getRegionInfo(null, null, $city['fiasRegionId']);
                    $citylist[$cityId]['RegionName'] = $region['name'];
                    $citylist[$cityId]['RegionId'] = $region['kladr'];

                }

            }

            //Ограничитель для тестов
            if($this->memcache instanceof \memcache) $citylist=array_slice($citylist,0,199);
            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $citylist, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $citylist;

        /*
        getPickupPoints

        Получение списка ПВЗ

        GET-запрос http://apiurl/getPickupPoints

        Пример ответа:

        [
        {
        "city": "Москва",
        "address": "Россия, Москва, Востряковский проезд, 10Бс19",
        "lat": 55.577271,
        "lng": 37.626052,
        "office": true,
        "guid": "67534813-855d-11e6-80c7-000d3a2542c4",
        "partner": "ПЭК",
        "schedule": "",
        "phone": ""
        },
        {
        "city": "Ярославль",
        "address": "Россия, Ярославль, проспект Октября, 93",
        "lat": 57.661655,
        "lng": 39.841954,
        "office": false,
        "guid": "352845f6-a017-11e6-80c7-000d3a2542c4",
        "partner": "ПЭК",
        "schedule": "Пн.-Пт. с 9-19, сб. с 10-16, вс.- вых",
        "phone": ""
        }
        ]
        */

    }
    public function parcelShopsList($cityName='', $regionName='', $weight=0, $size=0, $payment='', $number='') { //Получение списка точек самовывоза

        $namespace = __NAMESPACE__;

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(empty($parcelShopsList)) {

            $response = $this->authorization->query("{}", "getPickupPoints", "GET");
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

            foreach($response as $parcelShopId => $parcelShop) {

                if (array_key_exists('city', $parcelShop)) $parcelShopsList[$parcelShopId]['CitiName'] = $parcelShop['city'];
                if (array_key_exists('address', $parcelShop)) $parcelShopsList[$parcelShopId]['Address'] = $parcelShop['address'];
                if (array_key_exists('lat', $parcelShop)) $parcelShopsList[$parcelShopId]['Latitude'] = $parcelShop['lat'];
                if (array_key_exists('lng', $parcelShop)) $parcelShopsList[$parcelShopId]['Longitude'] = $parcelShop['lng'];
                //$parcelShopsList[$parcelShopId][''] = $parcelShop->office;
                if (array_key_exists('guid', $parcelShop)) $parcelShopsList[$parcelShopId]['Number'] = $parcelShop['guid'];
                if (array_key_exists('partner', $parcelShop)) $parcelShopsList[$parcelShopId]['OwnerName'] = $parcelShop['partner'];
                if (array_key_exists('schedule', $parcelShop)) $parcelShopsList[$parcelShopId]['WorkTime'] = $parcelShop['schedule'];
                if (array_key_exists('phone', $parcelShop)) $parcelShopsList[$parcelShopId]['Phone'] = $parcelShop['phone'];

                if($region = $this->getRegionInfo(null, null, $parcelShop['fiasRegionId'])) {

                    $parcelShopsList[$parcelShopId]['Region'] = $region['name'];

                } elseif($addressInfo = $this->getAddressInfo($parcelShop['address'])) {

                    $parcelShopsList[$parcelShopId]['Region'] = $addressInfo['region']['name'];

                }


                if (array_key_exists('MaxWeight', $parcelShop)) $parcelShopsList[$parcelShopId]['MaxWeight'] = $parcelShop['MaxWeight'];
                if (array_key_exists('MaxSize', $parcelShop)) $parcelShopsList[$parcelShopId]['MaxSize'] = $parcelShop['MaxSize'];
                if (!$parcelShop['MaxWeight']) {

                    if ($parcelShopsList[$parcelShopId]['OwnerName'] == 'ООО "Боксберри"') {
                        $parcelShopsList[$parcelShopId]['MaxWeight'] = 15;
                        $parcelShopsList[$parcelShopId]['MaxSize'] = 144;
                    } else {
                        $parcelShopsList[$parcelShopId]['MaxWeight'] = 500;
                        $parcelShopsList[$parcelShopId]['MaxSize'] = 1827;
                    }

                }
                $parcelShopsList[$parcelShopId]['Status'] = 2;

                $parcelShopsList[$parcelShopId]['Cash'] = true;
                if (array_key_exists('Card', $parcelShop)) $parcelShopsList[$parcelShopId]['Card'] = $parcelShop['Card'];
                else $parcelShopsList[$parcelShopId]['Card'] = true;

            }


            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $parcelShopsList, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $parcelShopsListReturn;

        /*
        getPickupPoints

        Получение списка ПВЗ

        GET-запрос http://apiurl/getPickupPoints

        Пример ответа:

        [
        {
        "city": "Москва",
        "address": "Россия, Москва, Востряковский проезд, 10Бс19",
        "lat": 55.577271,
        "lng": 37.626052,
        "office": true,
        "guid": "67534813-855d-11e6-80c7-000d3a2542c4",
        "partner": "ПЭК",
        "schedule": "",
        "phone": ""
        },
        {
        "city": "Ярославль",
        "address": "Россия, Ярославль, проспект Октября, 93",
        "lat": 57.661655,
        "lng": 39.841954,
        "office": false,
        "guid": "352845f6-a017-11e6-80c7-000d3a2542c4",
        "partner": "ПЭК",
        "schedule": "Пн.-Пт. с 9-19, сб. с 10-16, вс.- вых",
        "phone": ""
        }
        ]
        */

    }
    public function shippingPointsList() { //Получение списка точек сдачи
    }
    public function tariffList() { //Информация о тарифах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $tariffList[0]['id'] = "1";
        $tariffList[0]['name'] = "Авто до двери";
        $tariffList[0]['confines']['weight'] = "500";
        $tariffList[0]['mode'] = "Д-Д";
        $tariffList[0]['type'] = "Посылка";
        $tariffList[0]['description'] = "Услуга экономичной доставки товаров по России";

        $tariffList[1]['id'] = "2";
        $tariffList[1]['name'] = "Авто до ПВЗ";
        $tariffList[1]['confines']['weight'] = "500";
        $tariffList[1]['mode'] = "Д-С";
        $tariffList[1]['type'] = "Посылка";
        $tariffList[1]['description'] = "Услуга экономичной доставки товаров по России";

        $tariffList[2]['id'] = "3";
        $tariffList[2]['name'] = "Авиа до двери";
        $tariffList[2]['confines']['weight'] = "500";
        $tariffList[2]['mode'] = "Д-Д";
        $tariffList[2]['type'] = "Посылка";
        $tariffList[2]['description'] = "Услуга экспресс доставки товаров по России";

        $tariffList[3]['id'] = "4";
        $tariffList[3]['name'] = "Авиа до ПВЗ";
        $tariffList[3]['confines']['weight'] = "500";
        $tariffList[3]['mode'] = "Д-С";
        $tariffList[3]['type'] = "Посылка";
        $tariffList[3]['description'] = "Услуга экспресс доставки товаров по России";

        $tariffList[4]['id'] = "5";
        $tariffList[4]['name'] = "До ПВЗ Boxberry";
        $tariffList[4]['confines']['weight'] = "15";
        $tariffList[4]['mode'] = "Д-С";
        $tariffList[4]['type'] = "Посылка";
        $tariffList[4]['description'] = "Услуга доставки товаров по России";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffList;

    }
    public function errorList() { //Информация об ошибках

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $errors[0]['Error'] = 901;
        $errors[0]['ErrorText'] = "Не удалось прочитать JSON";

        $errors[1]['Error'] = 902;
        $errors[1]['ErrorText'] = "Поле <ИмяПоля> не заполнено";

        $errors[2]['Error'] = 903;
        $errors[2]['ErrorText'] = "Отсутствует обязательное поле <ИмяПоля>";

        $errors[3]['Error'] = 904;
        $errors[3]['ErrorText'] = "Пользователь не определен! Использование API не возможно!";

        $errors[4]['Error'] = 905;
        $errors[4]['ErrorText'] = "Не указан основной контрагент партнера! Использование API не возможно!";

        $errors[5]['Error'] = 906;
        $errors[5]['ErrorText'] = "Поле <ИмяПоля> не является числовым";

        $errors[6]['Error'] = 999;
        $errors[6]['ErrorText'] = "Неизвестная ошибка";

        $errors[7]['Error'] = 101;
        $errors[7]['ErrorText'] = "При выборе типа доставки ПВЗ необходимо заполнить pickupPointCode";

        $errors[8]['Error'] = 102;
        $errors[8]['ErrorText'] = "Значение pickupPointCode должно содержать GUID";

        $errors[9]['Error'] = 103;
        $errors[9]['ErrorText'] = "deliveryType дожно содержать значение от 1 до 5";

        $errors[10]['Error'] = 104;
        $errors[10]['ErrorText'] = "В <paymentMethod> указано значение, соответствующее наложенному платежу, значение <total> равно 0!";

        $errors[11]['Error'] = 105;
        $errors[11]['ErrorText'] = "Внутренняя ошибка. Не удалось создать заявку.";

        $errors[12]['Error'] = 106;
        $errors[12]['ErrorText'] = "Внутренняя ошибка. Неизвестный тип результата создания заявки.";

        $errors[13]['Error'] = 201;
        $errors[13]['ErrorText'] = "";

        $errors[14]['Error'] = 301;
        $errors[14]['ErrorText'] = "";

        $errors[15]['Error'] = 401;
        $errors[15]['ErrorText'] = "Не удалось геокодировать зону отправления";

        $errors[16]['Error'] = 402;
        $errors[16]['ErrorText'] = "Не удалось геокодировать зону получения";

        $errors[17]['Error'] = 403;
        $errors[17]['ErrorText'] = "Указан несуществующий тип доставки";

        $errors[18]['Error'] = 501;
        $errors[18]['ErrorText'] = "Не удалось получить информацию по заявкам";

        $errors[19]['Error'] = 601;
        $errors[19]['ErrorText'] = "";

        $errors[20]['Error'] = 701;
        $errors[20]['ErrorText'] = "";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $errors;

    }
    public function statusList() { //Информация об статусах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $statusList[0]['State'] = "9f02eabc-6aa3-11e6-80e9-003048baa05f";
        $statusList[0]['StateText'] = "Новый";
        $statusList[1]['State'] = "41552567-6b97-11e6-80e9-003048baa05f";
        $statusList[1]['StateText'] = "Ожидание";
        $statusList[2]['State'] = "65b5ceda-6b97-11e6-80e9-003048baa05f";
        $statusList[2]['StateText'] = "На складе";
        $statusList[3]['State'] = "7c4b94cd-6f5c-11e6-80ea-003048baa05f";
        $statusList[3]['StateText'] = "На складе (сортировка)";
        $statusList[4]['State'] = "b122101b-6f61-11e6-80ea-003048baa05f";
        $statusList[4]['StateText'] = "В пути";
        $statusList[5]['State'] = "bfcc82dc-6f8e-11e6-80ea-003048baa05f";
        $statusList[5]['StateText'] = "На складе ФС";
        $statusList[6]['State'] = "8628571a-6b97-11e6-80e9-003048baa05f";
        $statusList[6]['StateText'] = "На доставке";
        $statusList[7]['State'] = "f510368a-8973-11e6-80c7-000d3a2542c4";
        $statusList[7]['StateText'] = "Возврат в пути";
        $statusList[8]['State'] = "9aaa55ed-6b97-11e6-80e9-003048baa05f";
        $statusList[8]['StateText'] = "Возврат на складе";
        $statusList[9]['State'] = "1094ba91-8ca3-11e6-80c7-000d3a2542c4";
        $statusList[9]['StateText'] = "Возврат выдан";
        $statusList[10]['State'] = "b3e0596a-6b97-11e6-80e9-003048baa05f";
        $statusList[10]['StateText'] = "Выдан";
        $statusList[11]['State'] = "675f4358-6f61-11e6-80ea-003048baa05f";
        $statusList[11]['StateText'] = "На ПВЗ";
        $statusList[12]['State'] = "00a72c8b-7e4a-11e6-80c7-000d3a2542c4";
        $statusList[12]['StateText'] = "Отменен";
        $statusList[13]['State'] = "-1";
        $statusList[13]['StateText'] = "Не найден";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $statusList;

    }

}
