<?php

namespace pickpoint;

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
        $status[13]['active'] = true;
        $status[14]['method'] = "batches/getInfo";
        $status[14]['active'] = true;
        $status[15]['method'] = "batches/removeOrder";
        $status[15]['active'] = true;
        $status[16]['method'] = "allocates/courier";
        $status[16]['active'] = true;
        $status[17]['method'] = "allocates/cancel";
        $status[17]['active'] = true;
        $status[18]['method'] = "returns/create";
        $status[18]['active'] = true;
        $status[19]['method'] = "returns/getReturnsList";
        $status[19]['active'] = true;
        $status[20]['method'] = "returns/getInfo";
        $status[20]['active'] = true;


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

            $citylist = $this->authorization->query("{}", "citylist", "GET");
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

            foreach($citylist as $cityId => $city) {

                $citylist[$cityId]['name'] = $city['Name'];
                unset($citylist[$cityId]['Name']);

            }


            //if($this->memcache instanceof \memcache) $citylist=array_slice($citylist,0,299);
            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $citylist, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $citylist;

        /*
        Получение списка городов
        URL: /citylist
        Метод: GET

        Описание
        Команда предназначена для получения списка городов только для функции «Вызов курьера».
        Структура ответа
        [
        {
                "Id":		"<Id города>",
                "Owner_Id":	"<Owner_id города>",
                "name":		"<название города>",
                "RegionName":	"<название региона города>"
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

            $response = $this->authorization->query("{}", "postamatlist", "GET");
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

            foreach($response as $pvzId => $pvz) {

                $parcelShopsList[$pvzId] = json_decode(json_encode($pvz), TRUE);

                $MaxSize = explode('x', $parcelShopsList[$pvzId]['MaxSize']);
                if(sizeof($MaxSize)>1) {

                    $parcelShopsList[$pvzId]['MaxSize'] = $MaxSize[0]+$MaxSize[1]+$MaxSize[2];

                }
                if(!$parcelShopsList[$pvzId]['MaxSize']) $parcelShopsList[$pvzId]['MaxSize'] = 144;

                $parcelShopsList[$pvzId]['MaxWeight'] = intval($parcelShopsList[$pvzId]['MaxWeight']);

                $parcelShopsList[$pvzId]['Address'] = $parcelShopsList[$pvzId]['PostCode'].', '.$parcelShopsList[$pvzId]['Region'].', '.$parcelShopsList[$pvzId]['CitiName'].', '.$parcelShopsList[$pvzId]['Address'];

            }

            if($this->memcache instanceof \memcache) $citylist=array_slice($citylist,0,299);
            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $parcelShopsList, $cachetime);

        }


        foreach($parcelShopsList as $parcelShopId => $parcelShop) {

            if($parcelShop['Status'] == 3) unset($parcelShopsList[$parcelShopId]);
            if($payment && $parcelShop['AmountTo'] != 'Без ограничений' && $parcelShop['AmountTo'] < $declaredValue) unset($parcelShopsList[$parcelShopId]);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $parcelShopsListReturn;

        /*
        Получение списка терминалов
        URL: /postamatlist
        Метод: GET
        Описание
        Команда предназначена для получения списка постаматов в режиме «рабочий». Если постамат не может принять отправления или закрывается, он пропадает из данного списка. Список точек необходимо актуализировать 1 раз в сутки.
        Структура ответа
        [
        {
                "Id":		"<Id постамата (целое число)>",
                "OwnerId":	"<Owner_id постамата (целое число)>",
                "CitiId":		"<Id города (целое число)>",
                "CitiOwnerId":	"<Owner_id города (целое число)>",
                "CitiName":	"<Название города (50 символов)>",
                "Region":		"<Название региона (50 символов)>",
                "CountryName":	"<Название страны (50 символов)>",
                "Number":	"<номер постамат, (PTNumber) текст (8 символов)>",
                "Metro":		"<название ближайшей станция метро  (100 символов)>",
                "MetroArray":	"<список ближайших станций метро в виде массива>",
        [
                "метро 1 (50 символов)",
                …
                "метро n (50 символов)"
        ],

                "IndoorPlace":	"<описание входа к постамату (255 символов)>",
                "Address":	"<адрес расположения постамата (150 символов)>",
                "House":		"<номер дома (150 символов)>",
                "PostCode":	"<почтовый индекс (20 символов)>",
                "name":		"<название (80 символов)>",
                "WorkTime":	"<интервалы рабочего времени постамата >",//чч.мм-чч.мм,чч.мм-чч.мм,NODAY, …
        //по всем дням недели
                "Latitude":	"<Широта>",
                "Longitude":	"<Долгота>",
                "Status":		"<Статус постамата: 1 – новый, 2 – рабочий, 3 - закрытый>",

                "TypeTitle":	"<Тип терминала: АПТ/ПВЗ>",
                "Cash":		"<Возможность оплаты наличными: 0 – нет, 1 – да>",
                "Card":		"<Возможность оплаты пластиковой картой: 0 – нет, 1 –да>",
                "AmountTo": "<Максимаьная сумма оплаты>",
                "InDescription":	"<Полное описание местонахождения терминала внутри (8000 символов)>",
                "OutDescription":	"<Полное описание местонахождения терминала снаружи (8000 символов)>",
                "MaxSize":	"<текстовое описание максимального размера одного из видов:
        - 36х36х60,
        - max сумма 3х измерений - 80 см>",
                "MaxWeight":	"<текстовое описание максимального веса отправления вида: 5 кг>",
                "WorkHourly":	"<true/false – работает круглосуточно>",
                "Opening":	"<true/false – разрешено вскрытие>",
                "Returning":	"<true/false – возможен возврат>",
                "Fitting":		"<true/false – возможна примерка>",
                "LocationType":	"<тип размещения – 1 – в помещении, 2 – на улице>"
        "OwnerName":        "<название сети постаматов (100 символов)>",
        "BuildingType":        "<название типа строения, в котором расположена точка>",
        "Comment":            "<комментарий>"
        }
        ]
        •	АПТ – Автоматизированный Посылочный Терминал (постамат)
        •	ПВЗ – Пункт Выдачи Заказов.
        */

    }
    public function shippingPointsList() { //Получение списка точек сдачи
    }
    public function tariffList() { //Информация о тарифах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $tariffList[0]['name'] = "Доставка до ПВЗ";
        $tariffList[0]['id'] = "1";
        $tariffList[0]['mode'] = "Д-С";
        $tariffList[0]['confines']['weight'] = "31";
        $tariffList[0]['confines']['length'] = "60";
        $tariffList[0]['confines']['depth'] = "60";
        $tariffList[0]['confines']['width'] = "60";
        $tariffList[0]['type'] = "Классическая доставка";
        $tariffList[0]['description'] = "Доставка тяжелых посылок и сборных грузов по территории России.";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffList;

    }
    public function errorList() { //Информация об ошибках

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $errors[0]['Error'] = 0;
        $errors[0]['ErrorText'] = "Операция выполнена успешно";

        $errors[1]['Error'] = -1;
        $errors[1]['ErrorText'] = "Непредвиденная ошибка";

        $errors[2]['Error'] = 1;
        $errors[2]['ErrorText'] = "Неверная сессия или сессия истекла";

        $errors[3]['Error'] = 10;
        $errors[3]['ErrorText'] = "Неверный логин или пароль";

        $errors[4]['Error'] = 20;
        $errors[4]['ErrorText'] = "Неверные параметры запроса";

        $errors[5]['Error'] = 21;
        $errors[5]['ErrorText'] = "Данные не найдены";

        $errors[6]['Error'] = 25;
        $errors[6]['ErrorText'] = "Отправление не найдено";

        $errors[7]['Error'] = 30;
        $errors[7]['ErrorText'] = "Неверный номер контракта";

        $errors[8]['Error'] = 35;
        $errors[8]['ErrorText'] = "Субклиент не найден";

        $errors[9]['Error'] = 100;
        $errors[9]['ErrorText'] = "Временная ошибка";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $errors;

    }
    public function statusList() { //Информация об статусах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $statusList = $this->authorization->query("{}", "getstates", "GET");
        $statusList = json_encode($statusList, JSON_UNESCAPED_UNICODE);
        $statusList = json_decode($statusList, true);


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $statusList;

        /*
        Получение справочника статусов отправления
        URL: /getstates
        Метод: GET

        Описание
        Команда предназначена для получения списка возможных статусов отправлений.
        Структура ответа
        [
        {
                "State":		"<номер статута>",
                "StateText":	"<текстовое описание статуса>"
        }
        ]
        */

    }

}
