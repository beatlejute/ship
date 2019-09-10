<?php

namespace shiptor;

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
        $status[9]['active'] = true;
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
        $status[16]['active'] = true;
        $status[17]['method'] = "allocates/cancel";
        $status[17]['active'] = true;
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

            $citylist = [];

            $query['id'] = "JsonRpcClient.js";
            $query['jsonrpc'] = "2.0";
            $query['method'] = "getSettlements";
            //$query['params']['per_page'] = 100;
            $query['params']['page'] = 1;
            $query['params']['types'] = ["Город"];
            //$query['params']['level'] = 3;
            //$query['params']['parent'] = "02000000000";
            $query['params']['country_code'] = "RU";



                $response = $this->authorization->query(json_encode($query), "public/v1", "POST");
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



                foreach($response['result']['settlements'] as $cityId => $city) {

                    //Ограничитель для тестов
                    if(!($this->memcache instanceof \memcache) || $city['type']=='Город') {

                        $citylist[$cityId]['Id'] = $city['kladr_id'];
                        $citylist[$cityId]['name'] = $city['name'];
                        if (sizeof($city['parents'])) $citylist[$cityId]['RegionName'] = $city['parents'][0]['name'] . ' ' . $city['parents'][0]['type_short'] . '.';
                        if (sizeof($city['parents'])) $citylist[$cityId]['RegionId'] = $city['parents'][0]['kladr_id'];
                        $citylist[$cityId]['kladr'] = $city['kladr_id'];

                    }

                }

            //}


            //Ограничитель для тестов
            //if($this->memcache instanceof \memcache) $citylist=array_slice($citylist,0,2999);
            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $citylist, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $citylist;

        /*
        Public - getSettlements — Получение справочника населенных пунктов
        POST
        https://api.shiptor.ru/public/v1
        Разрешено: Всем

        Параметр

        Название	Тип	    Описание
        id	        String  По умолчанию: JsonRpcClient.js

        jsonrpc	    Number  По умолчанию: 2.0

        method	    String  По умолчанию: getSettlements


        params	    Object
         per_page	 Number  Сколько выводить на страницу

         page	     Number  Какая страница

         types необязательный	Array   Тип

         level	     Number  Уровень

         parent	     String  Кладр родителя

         country_code	String  Индетификатор страны

                                Допустимые значения: "RU", "KZ", "RU"


        Пример запроса:
        {
            "id": "JsonRpcClient.js",
            "jsonrpc": "2.0",
            "method": "getSettlements",
            "params": {
                "per_page": 10,
                "page": 1,
                "types": [
                    "Город"
                ],
                "level": 3,
                "parent": "02000000000",
                "country_code": "RU"
            }
        }

        Результат успешного выполнения:
        HTTP/1.1 200 OK
        {
          "jsonrpc": "2.0",
          "result": [
            {
              "count": 166045,
              "page": 1,
              "per_page": 10,
              "pages": 16605,
              "settlements": [
                {
                  "kladr_id": "05000008000",
                  "name": "Кизляр",
                  "type": "Город",
                  "type_short": "г",
                  "parents": [
                    {
                      "kladr_id": "05000000000",
                      "name": "Дагестан",
                      "type": "Республика",
                      "type_short": "Респ"
                    }
                  ]
                },
                [...]
              ]
            },
            [...]
          ],
          "id": "JsonRpcClient.js"
        }


        -32602
        Название	        Тип	    Описание
        InvalidCountryCode	Object  Invalid country code. Allowed codes: ...

        */

    }
    public function parcelShopsList($cityName='', $regionName='', $weight=0, $size=0, $payment='', $number='', $tariff='') { //Получение списка точек самовывоза

        $namespace = __NAMESPACE__;

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(empty($parcelShopsList)) {

            $query['id'] = "JsonRpcClient.js";
            $query['jsonrpc'] = "2.0";
            $query['method'] = "getDeliveryPoints";
            $query['params'] = [];

            $response = $this->authorization->query(json_encode($query), "public/v1", "POST");
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

            $response = $response['result'];

            foreach($response as $pvzId => $pvz) {

                if(array_key_exists('id', $pvz)) $parcelShopsList[$pvzId]["Id"] = $pvz['id'];
                if(array_key_exists('id', $pvz)) $parcelShopsList[$pvzId]["Number"] = $pvz['id']."";
                if(array_key_exists('courier', $pvz)) $parcelShopsList[$pvzId]["OwnerName"] = $pvz['courier'];
                if(array_key_exists('address', $pvz)) $parcelShopsList[$pvzId]["Address"] = trim($pvz['address']);
                if(array_key_exists('phones', $pvz)) $parcelShopsList[$pvzId]["Phone"] = $pvz['phones'][0];
                if(array_key_exists('trip_description', $pvz)) $parcelShopsList[$pvzId]["OutDescription"] = $pvz['trip_description'];
                if(array_key_exists('work_schedule', $pvz)) $parcelShopsList[$pvzId]["WorkTime"] = $pvz['work_schedule'];
                if(array_key_exists('card', $pvz)) $parcelShopsList[$pvzId]["Card"] = $pvz['card'];
                if(array_key_exists('gps_location', $pvz)) $parcelShopsList[$pvzId]["Latitude"] = $pvz['gps_location']['latitude'];
                if(array_key_exists('gps_location', $pvz)) $parcelShopsList[$pvzId]["Longitude"] = $pvz['gps_location']['longitude'];
                if(array_key_exists('MaxWeight', $pvz['limits'])) $parcelShopsList[$pvzId]["MaxWeight"] = $pvz['limits']['MaxWeight'];
                if(array_key_exists('MaxSize', $pvz['limits'])) $parcelShopsList[$pvzId]["MaxSize"] = $pvz['limits']['MaxSize'];
                if(!$parcelShopsList[$pvzId]["MaxWeight"] || !$parcelShopsList[$pvzId]["MaxSize"]) switch ($parcelShopsList[$pvzId]["OwnerName"]) {
                    case 'boxberry':
                        $parcelShopsList[$pvzId]["MaxWeight"] = $pvz['limits']['max_weight']['value'] ?: 15;
                        $parcelShopsList[$pvzId]["MaxSize"] = $pvz['limits']['volume']*300 ?: 144;
                        break;
                    case 'shiptor':
                        $parcelShopsList[$pvzId]["MaxWeight"] = 31;
                        $parcelShopsList[$pvzId]["MaxSize"] = 180;
                        break;
                    case 'pickpoint':
                        $parcelShopsList[$pvzId]["MaxWeight"] = 31;
                        $parcelShopsList[$pvzId]["MaxSize"] = 180;
                        break;
                    case 'cdek':
                        $parcelShopsList[$pvzId]["MaxWeight"] = 29;
                        $parcelShopsList[$pvzId]["MaxSize"] = 450;
                        break;
                    case 'iml':
                        $parcelShopsList[$pvzId]["MaxWeight"] = 30;
                        $parcelShopsList[$pvzId]["MaxSize"] = 540;
                        break;
                    case 'dpd':
                        $parcelShopsList[$pvzId]["MaxWeight"] = $pvz['limits']['max_weight']['value'] ?: 10;
                        $parcelShopsList[$pvzId]["MaxSize"] = $pvz['limits']['dimension_sum'] ?: 100;
                        break;
                }
                if(array_key_exists('prepare_address', $pvz)) {
                    $parcelShopsList[$pvzId]["Region"] = $pvz['prepare_address']['administrative_area'];
                    $parcelShopsList[$pvzId]["Region"] = str_replace(' обл', ' обл.', $parcelShopsList[$pvzId]["Region"]);
                    $parcelShopsList[$pvzId]["Region"] = str_replace(' обл..', ' обл.', $parcelShopsList[$pvzId]["Region"]);
                    $parcelShopsList[$pvzId]["Region"] = trim($parcelShopsList[$pvzId]["Region"]);
                }
                if(array_key_exists('prepare_address', $pvz)) {
                    $parcelShopsList[$pvzId]["CitiName"] = $pvz['prepare_address']['settlement'];
                }

                $parcelShopsList[$pvzId]["kladr"] = $pvz['kladr_id'];
                if(strlen($parcelShopsList[$pvzId]["kladr"]) == 11) $parcelShopsList[$pvzId]["kladr"] .= '00';

                if($parcelShopsList[$pvzId]["Address"]{0} == ',') $parcelShopsList[$pvzId]["Address"] = $parcelShopsList[$pvzId]["CitiName"] ? $parcelShopsList[$pvzId]["CitiName"].$parcelShopsList[$pvzId]["Address"] : $parcelShopsList[$pvzId]["Region"].$parcelShopsList[$pvzId]["Address"];

                if(array_key_exists('prepare_address', $pvz))  $parcelShopsList[$pvzId]["Street"] = $pvz['prepare_address']['street'];
                if(array_key_exists('prepare_address', $pvz)) $parcelShopsList[$pvzId]["House"] = $pvz['prepare_address']['house'];
                if(array_key_exists('prepare_address', $pvz)) $parcelShopsList[$pvzId]["PostCode"] = $pvz['prepare_address']['postal_code'];
                $parcelShopsList[$pvzId]['Status'] = 2;

                if($pvz['shipping_methods']) $parcelShopsList[$pvzId]["tariffs"] = $pvz['shipping_methods'];


                if(!$parcelShopsList[$pvzId]["CitiName"]) {

                    switch ($parcelShopsList[$pvzId]["OwnerName"]) {
                        case 'pickpoint':
                            $address = explode(",", $parcelShopsList[$pvzId]["Address"]);
                            $parcelShopsList[$pvzId]['CitiName'] = trim($address[1]);
                            $parcelShopsList[$pvzId]['Region'] = trim($address[0]);
                            break;
                        case 'boxberry':
                            $address = explode(",", $parcelShopsList[$pvzId]["Address"]);
                            $parcelShopsList[$pvzId]['CitiName'] = trim($address[1]);
                            break;
                        case 'cdek':
                            $address = explode(",", $parcelShopsList[$pvzId]["Address"]);
                            $parcelShopsList[$pvzId]['CitiName'] = trim($address[0]);
                            break;
                        case 'iml':
                            $address = explode(",", $parcelShopsList[$pvzId]["Address"]);
                            $parcelShopsList[$pvzId]['CitiName'] = trim($address[0]);
                            break;
                        case 'dpd':
                            $address = explode(",", $parcelShopsList[$pvzId]["Address"]);
                            //$addressObl = explode(" ", $address[0]);
                            $parcelShopsList[$pvzId]['CitiName'] = trim($address[1]);
                            //$parcelShopsList[$pvzId]['Region'] = trim($addressObl[1] . ' обл.');
                            break;
                    }

                }


                $parcelShopsList[$pvzId]["CitiName"] = str_replace('г ', '', $parcelShopsList[$pvzId]["CitiName"]);
                $parcelShopsList[$pvzId]["CitiName"] = str_replace('с ', '', $parcelShopsList[$pvzId]["CitiName"]);
                $parcelShopsList[$pvzId]["CitiName"] = trim($parcelShopsList[$pvzId]["CitiName"]);


                switch (substr($parcelShopsList[$pvzId]["kladr"], 0, 2)) {

                    case '01':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Адыгея';
                        break;
                    case '04':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Алтай';
                        break;
                    case '22':
                        $parcelShopsList[$pvzId]['Region'] = 'Алтайский край';
                        break;
                    case '28':
                        $parcelShopsList[$pvzId]['Region'] = 'Амурская обл.';
                        break;
                    case '29':
                        $parcelShopsList[$pvzId]['Region'] = 'Архангельская обл.';
                        break;
                    case '30':
                        $parcelShopsList[$pvzId]['Region'] = 'Астраханская обл.';
                        break;
                    case '99':
                        $parcelShopsList[$pvzId]['Region'] = 'Байконур';
                        break;
                    case '02':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Башкортостан';
                        break;
                    case '31':
                        $parcelShopsList[$pvzId]['Region'] = 'Белгородская обл.';
                        break;
                    case '32':
                        $parcelShopsList[$pvzId]['Region'] = 'Брянская обл.';
                        break;
                    case '03':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Бурятия';
                        break;
                    case '33':
                        $parcelShopsList[$pvzId]['Region'] = 'Владимирская обл.';
                        break;
                    case '34':
                        $parcelShopsList[$pvzId]['Region'] = 'Волгоградская обл.';
                        break;
                    case '35':
                        $parcelShopsList[$pvzId]['Region'] = 'Вологодская обл.';
                        break;
                    case '36':
                        $parcelShopsList[$pvzId]['Region'] = 'Воронежская обл.';
                        break;
                    case '05':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Дагестан';
                        break;
                    case '79':
                        $parcelShopsList[$pvzId]['Region'] = 'Аобл. Еврейская';
                        break;
                    case '75':
                        $parcelShopsList[$pvzId]['Region'] = 'Забайкальский край';
                        break;
                    case '80':
                        $parcelShopsList[$pvzId]['Region'] = 'округ. Забайкальский край Агинский Бурятский';
                        break;
                    case '37':
                        $parcelShopsList[$pvzId]['Region'] = 'Ивановская обл.';
                        break;
                    case '06':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Ингушетия';
                        break;
                    case '38':
                        $parcelShopsList[$pvzId]['Region'] = 'Иркутская обл.';
                        break;
                    case '85':
                        $parcelShopsList[$pvzId]['Region'] = 'округ. Иркутская обл Усть-Ордынский Бурятский';
                        break;
                    case '07':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Кабардино-Балкарская';
                        break;
                    case '39':
                        $parcelShopsList[$pvzId]['Region'] = 'Калининградская обл.';
                        break;
                    case '08':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Калмыкия';
                        break;
                    case '40':
                        $parcelShopsList[$pvzId]['Region'] = 'Калужская обл.';
                        break;
                    case '41':
                        $parcelShopsList[$pvzId]['Region'] = 'Камчатский край';
                        break;
                    case '09':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Карачаево-Черкесская';
                        break;
                    case '10':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Карелия';
                        break;
                    case '42':
                        $parcelShopsList[$pvzId]['Region'] = 'Кемеровская обл.';
                        break;
                    case '43':
                        $parcelShopsList[$pvzId]['Region'] = 'Кировская обл.';
                        break;
                    case '11':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Коми';
                        break;
                    case '81':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Коми-Пермяцкий';
                        break;
                    case '82':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Корякский';
                        break;
                    case '44':
                        $parcelShopsList[$pvzId]['Region'] = 'Костромская обл.';
                        break;
                    case '23':
                        $parcelShopsList[$pvzId]['Region'] = 'Краснодарский край';
                        break;
                    case '24':
                        $parcelShopsList[$pvzId]['Region'] = 'Красноярский край';
                        break;
                    case '91':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Крым';
                        break;
                    case '45':
                        $parcelShopsList[$pvzId]['Region'] = 'Курганская обл.';
                        break;
                    case '46':
                        $parcelShopsList[$pvzId]['Region'] = 'Курская обл.';
                        break;
                    case '47':
                        $parcelShopsList[$pvzId]['Region'] = 'Ленинградская обл.';
                        break;
                    case '48':
                        $parcelShopsList[$pvzId]['Region'] = 'Липецкая обл.';
                        break;
                    case '49':
                        $parcelShopsList[$pvzId]['Region'] = 'Магаданская обл.';
                        break;
                    case '12':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Марий Эл';
                        break;
                    case '13':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Мордовия';
                        break;
                    case '77':
                        $parcelShopsList[$pvzId]['Region'] = 'Москва';
                        $parcelShopsList[$pvzId]['CitiName'] = 'Москва';
                        break;
                    case '50':
                        $parcelShopsList[$pvzId]['Region'] = 'Московская обл.';
                        break;
                    case '51':
                        $parcelShopsList[$pvzId]['Region'] = 'Мурманская обл.';
                        break;
                    case '83':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Ненецкий';
                        break;
                    case '52':
                        $parcelShopsList[$pvzId]['Region'] = 'Нижегородская обл.';
                        break;
                    case '53':
                        $parcelShopsList[$pvzId]['Region'] = 'Новгородская обл.';
                        break;
                    case '54':
                        $parcelShopsList[$pvzId]['Region'] = 'Новосибирская обл.';
                        break;
                    case '55':
                        $parcelShopsList[$pvzId]['Region'] = 'Омская обл.';
                        break;
                    case '56':
                        $parcelShopsList[$pvzId]['Region'] = 'Оренбургская обл.';
                        break;
                    case '57':
                        $parcelShopsList[$pvzId]['Region'] = 'Орловская обл.';
                        break;
                    case '58':
                        $parcelShopsList[$pvzId]['Region'] = 'Пензенская обл.';
                        break;
                    case '59':
                        $parcelShopsList[$pvzId]['Region'] = 'Пермский край';
                        break;
                    case '25':
                        $parcelShopsList[$pvzId]['Region'] = 'Приморский край';
                        break;
                    case '60':
                        $parcelShopsList[$pvzId]['Region'] = 'Псковская обл.';
                        break;
                    case '61':
                        $parcelShopsList[$pvzId]['Region'] = 'Ростовская обл.';
                        break;
                    case '62':
                        $parcelShopsList[$pvzId]['Region'] = 'Рязанская обл.';
                        break;
                    case '63':
                        $parcelShopsList[$pvzId]['Region'] = 'Самарская обл.';
                        break;
                    case '78':
                        $parcelShopsList[$pvzId]['Region'] = 'Санкт-Петербург';
                        $parcelShopsList[$pvzId]['CitiName'] = 'Санкт-Петербург';
                        break;
                    case '64':
                        $parcelShopsList[$pvzId]['Region'] = 'Саратовская обл.';
                        break;
                    case '14':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Саха /Якутия/';
                        break;
                    case '65':
                        $parcelShopsList[$pvzId]['Region'] = 'Сахалинская обл.';
                        break;
                    case '66':
                        $parcelShopsList[$pvzId]['Region'] = 'Свердловская обл.';
                        break;
                    case '92':
                        $parcelShopsList[$pvzId]['Region'] = 'Севастополь';
                        $parcelShopsList[$pvzId]['CitiName'] = 'Севастополь';
                        break;
                    case '15':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Северная Осетия - Алания';
                        break;
                    case '67':
                        $parcelShopsList[$pvzId]['Region'] = 'Смоленская обл.';
                        break;
                    case '26':
                        $parcelShopsList[$pvzId]['Region'] = 'Ставропольский край';
                        break;
                    case '84':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Таймырский (Долгано-Ненецкий)';
                        break;
                    case '68':
                        $parcelShopsList[$pvzId]['Region'] = 'Тамбовская обл.';
                        break;
                    case '16':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Татарстан';
                        break;
                    case '69':
                        $parcelShopsList[$pvzId]['Region'] = 'Тверская обл.';
                        break;
                    case '70':
                        $parcelShopsList[$pvzId]['Region'] = 'Томская обл.';
                        break;
                    case '71':
                        $parcelShopsList[$pvzId]['Region'] = 'Тульская обл.';
                        break;
                    case '17':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Тыва';
                        break;
                    case '72':
                        $parcelShopsList[$pvzId]['Region'] = 'Тюменская обл.';
                        break;
                    case '18':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Удмуртская';
                        break;
                    case '73':
                        $parcelShopsList[$pvzId]['Region'] = 'Ульяновская обл.';
                        break;
                    case '27':
                        $parcelShopsList[$pvzId]['Region'] = 'Хабаровский край';
                        break;
                    case '19':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Хакасия';
                        break;
                    case '86':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Ханты-Мансийский Автономный округ - Югра';
                        break;
                    case '74':
                        $parcelShopsList[$pvzId]['Region'] = 'Челябинская обл.';
                        break;
                    case '20':
                        $parcelShopsList[$pvzId]['Region'] = 'Республика Чеченская';
                        break;
                    case '21':
                        $parcelShopsList[$pvzId]['Region'] = 'Чувашия. Чувашская Республика -';
                        break;
                    case '87':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Чукотский';
                        break;
                    case '88':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Эвенкийский';
                        break;
                    case '89':
                        $parcelShopsList[$pvzId]['Region'] = 'Автономный округ Ямало-Ненецкий';
                        break;
                    case '76':
                        $parcelShopsList[$pvzId]['Region'] = 'Ярославская обл.';
                        break;
                }

                /*if(!$parcelShopsList[$pvzId]["CitiName"]) {

                    $cacheKey = "cityListgetkladr" . $parcelShopsList[$pvzId]["CitiName"];

                    if ($this->memcache) $citykladr = $this->memcache->get($cacheKey);

                    if (empty($citykladr)) {

                        $citykladr = json_decode(file_get_contents("https://kladr-api.ru/api.php?contentType=city&query=" . $parcelShopsList[$pvzId]["CitiName"] . "&withParent=1&limit=1"), true);

                        if ($this->memcache) $this->memcache->set($cacheKey, $citykladr);
                    }

                    if ($citykladr['result'][0]['name']) {
                        $parcelShopsList[$pvzId]["CitiName"] = $citykladr['result'][0]['name'];
                    }

                    if (array_key_exists(0, $citykladr['result'][0]['parents']) && !$parcelShopsList[$pvzId]["Region"]) {

                        if ($citykladr['result'][0]['parents'][0]['name']) {

                            $parcelShopsList[$pvzId]['Region'] = $citykladr['result'][0]['parents'][0]['name'] . " " . $citykladr['result'][0]['parents'][0]['typeShort'] . ".";

                        }

                    }

                }*/


            }


            //Ограничитель для тестов
            if($this->memcache instanceof \memcache) $parcelShopsList=array_slice($parcelShopsList,0,499);

            if($this->memcache) $this->memcache->set($cacheKey, $parcelShopsList);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $parcelShopsListReturn;

        /*
          Public - getDeliveryPoints — Получение списка ПВЗ
            POST
            https://api.shiptor.ru/public/v1
            Разрешено: Всем

            Параметр
            Название	        Тип	        Описание
            id	                String	    По умолчанию: JsonRpcClient.js

            jsonrpc	            Number	    По умолчанию: 2.0

            method	            String	    По умолчанию: getDeliveryPoints

            params	            Object
              kladr_id	        String	    Код КЛАДР населенного пункта, можно получить из справочника населенных пунктов

              courier	        String	    Названия службы доставки

              shipping_method	Number	    Идентификатор метода доставки

              cod необ.         Boolean 	ПВЗ с наложенным платежом

              card необ.	    Boolean	    ПВЗ принимает безналичный расчёт

              limits необ.	    Object
                weight необ.	Number      Вес

                length необ.	Number      Длина

                width необ.	    Number      Ширина

                height необ.	Number      Высота


            Пример запроса:
            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "getDeliveryPoints",
                "params": {
                    "kladr_id": "36000001000",
                    "courier": "dpd",
                    "shipping_method": 5,
                    "limits": {
                        "weight": 30,
                        "length": 30,
                        "width": 30,
                        "height": 30
                    }
                }
            }


            Результат успешного выполнения:
            HTTP/1.1 200 OK
            {
              "jsonrpc": "2.0",
              "result": [
                {
                  "id": 1346,
                  "courier": "dpd",
                  "address": "385000 Адыгея, Майкоп, ул Жуковского, 22",
                  "phones": [],
                  "trip_description": null,
                  "work_schedule": "Пн,Вт,Ср,Чт,Пт: 08:00 - 17:00\r\nСб,Вс: выходной",
                  "shipping_days": null,
                  "cod": true,
                  "gps_location": [
                    "latitude": "44.607448",
                    "longitude": "40.108121",
                  ],
                  "kladr_id": "36000001000",
                  "shipping_method": [
                    7,
                    5,
                    6,
                  ],
                },
                [...]
              ],
              "id": "JsonRpcClient.js"
            }
         */
    }
    public function shippingPointsList() { //Получение списка точек сдачи
    }
    public function tariffList() { //Информация о тарифах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "getShippingMethods";
        $query['params'] = [];

        $response = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);

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

        $response = $response['result'];

        foreach($response as $tariffId => $tariff) {

            $tariffList[$tariffId]['id'] = $tariff['id'];
            $tariffList[$tariffId]['name'] = $tariff['name'];
            switch ($tariff['category']) {
            case 'delivery-point':
                    $tariffList[$tariffId]['mode'] = 'С-С';
                    break;
                case 'delivery-point-to-delivery-point':
                    $tariffList[$tariffId]['mode'] = 'С-С';
                    break;
                case 'to-door':
                    $tariffList[$tariffId]['mode'] = 'С-Д';
                    break;
                case 'delivery-point-to-door':
                    $tariffList[$tariffId]['mode'] = 'С-Д';
                    break;
                case 'door-to-door':
                    $tariffList[$tariffId]['mode'] = 'Д-Д';
                    break;
                case 'door-to-delivery-point':
                    $tariffList[$tariffId]['mode'] = 'Д-С';
                    break;
                case 'post-office':
                    $tariffList[$tariffId]['mode'] = 'С-Д';
                    break;
            }
            $tariffList[$tariffId]['type'] = $tariff['courier'];
            $tariffList[$tariffId]['description'] = $tariff['description'];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffList;

        /*
         Shipping - getShippingMethods — Получение справочника способов доставки

            POST
            https://api.shiptor.ru/shipping/v1
            Разрешено: По токену

          Параметр

            Название	    Тип	        Описание
            id	            String      По умолчанию: JsonRpcClient.js

            jsonrpc	        Number      По умолчанию: 2.0

            method	        String      По умолчанию: getShippingMethods

            params	        Object


          Пример запроса:

            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "getShippingMethods",
                "params": {}
            }

         Результат успешного выполнения:

            HTTP/1.1 200 OK
            {
              "jsonrpc": "2.0",
              "result": [
                {
                  "id": 11,
                  "name": "PickPoint",
                  "category": "delivery-point",
                  "group": "pickpoint",
                  "courier": "pickpoint",
                  "comment": "Комментарий к методу"
                },
                [...]
              ],
              "id": "JsonRpcClient.js"
            }

         */

    }
    public function errorList() { //Информация об ошибках

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $errors[0]['Error'] = '-32600';
        $errors[0]['ErrorText'] = 'Invalid RPC request. Not conforming to specification.';

        $errors[1]['Error'] = '-32601';
        $errors[1]['ErrorText'] = 'Requested method does not exist.';

        $errors[2]['Error'] = '-32602';
        $errors[2]['ErrorText'] = 'Wrong number of parameters or invalid method parameters.';

        $errors[3]['Error'] = '-32603';
        $errors[3]['ErrorText'] = 'Not authorized to call the requested method (No login or invalid login data was given).';

        $errors[4]['Error'] = '-32604';
        $errors[4]['ErrorText'] = 'Forbidden to call the requested method (but a valid login was given).';

        $errors[5]['Error'] = '-32605';
        $errors[5]['ErrorText'] = 'The RPC API has not been enabled in the configuration';

        $errors[6]['Error'] = '-32700';
        $errors[6]['ErrorText'] = 'Parse Error. Request not well formed.';

        $errors[7]['Error'] = '-32800';
        $errors[7]['ErrorText'] = 'Recursive calls to system.multicall are forbidden.';

        $errors[10]['Error'] = '-99999';
        $errors[10]['ErrorText'] = 'Unknown server error';


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $errors;

    }
    public function statusList() { //Информация об статусах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['id'] = "JsonRpcClient.js";
        $query['jsonrpc'] = "2.0";
        $query['method'] = "getPackageStatuses";
        $query['params'] = [];

        $response = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);

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

        $response = $response['result'];

        foreach($response as $statusId => $status) {

            $statusList[$statusId]['State'] = $statusId;
            $statusList[$statusId]['StateText'] = $status;

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $statusList;

        /*
         Shipping - getPackageStatuses — Получение списка статусов посылок

            POST
            https://api.shiptor.ru/shipping/v1
            Разрешено: Using API token

         Параметр
            Название            Тип	        Описание
            id	                String      По умолчанию: JsonRpcClient.js

            jsonrpc	            Number      По умолчанию: 2.0

            method	            String      По умолчанию: getPackagesStatuses

         Request-Example:
            {
                "id": "JsonRpcClient.js",
                "jsonrpc": "2.0",
                "method": "getPackageStatuses",
                "params": []
            }

         Success-Response:
             HTTP/1.1 200 OK
            {
                "jsonrpc": "2.0",
                "result": {
                    "new": "Новая",
                    "arrived-to-warehouse": "Прибыла на склад",
                    "packed": "Упакована",
                    "prepared-to-send": "Готова к отправке",
                    "sent": "Отправлена",
                    "delivered": "Доставлена",
                    "removed": "Удалена",
                    "recycled": "Утилизирована",
                    "waiting-pickup": "Ожидает забора",
                    "returned": "Возвращена",
                    "reported": "Возвращена отправителю",
                    "lost": "Утеряна",
                    "resend": "Отправлена повторно",
                    "waiting-on-delivery-point": "Ожидает на пункте выдачи",
                    "awaiting-return": "Ожидает решения по возврату"
                },
                "id": "JsonRpcClient.js"
            }
         */
    }

}
?>