<?php

namespace cdek;

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

            $pvzlist = $this->authorization->query("{}", "pvzlist.php", "GET", "xml");
            if(isset($pvzlist['error'])) return ['error' => $pvzlist['error']];


            if(!$pvzlist) {

                $answer['error']['code'] = "_1";
                $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

                return $answer;

            }
            if(!is_array($pvzlist)) {

                $answer['error']['code'] = "_2";
                $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                $answer['error']['info'][] = print_r($pvzlist, true);

                return $answer;

            }

            if($pvzlist['Pvz']['@attributes']) $pvzlist['Pvz'] = [$pvzlist['Pvz']];

            foreach($pvzlist['Pvz'] as $pvzId => $pvz) if(!$citysExist[$pvz['@attributes']['CityCode']]) {

                $citysExist[$pvz['@attributes']['CityCode']] = true;

                $citylist[$pvzId]['name'] = $pvz['@attributes']['City'];
                $citylist[$pvzId]['Id'] = $pvz['@attributes']['CityCode'];
                //$citylist[$pvzId]['RegionId'] = $pvz['@attributes']['RegionCode'];
                $citylist[$pvzId]['RegionName'] = $pvz['@attributes']['RegionName'];

            }


            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $citylist, $cachetime);

        }

        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $citylist;

        /*
        Список пунктов выдачи заказов (ПВЗ СДЭК и почтоматы партнеров)
        Документ «Список пунктов выдачи заказов» предоставляет список ПВЗ, действующих на момент запроса.
        Для использования необходимо отправить GET запрос на URL: <сервер>/pvzlist.php, например, https://integration.cdek.ru/pvzlist.php, с заполненными переменными $_GET['cityid'], $_GET['citypostcode' ], $_GET['type' ].
        Описание передаваемых данных:
        Параметр	Описание	Обязательность
        cityid	Код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls», где XXX – трехбуквенный код страны)	Нет
        citypostcode	Почтовый индекс города, для которого необходим список ПВЗ	Нет
        type	Тип пункта выдачи (ПВЗ или почтомат).
        Может принимать значения:
        «PVZ» - для отображения только складов СДЭК.
        «POSTOMAT» - для отображения почтоматов партнёра.
         «ALL» - для отображения всех ПВЗ не зависимо от их типа.
        При отсутствии параметра принимается значение по умолчанию «PVZ» для поддержания работы старых алгоритмов.	Нет
        countryid	Коды стран из нашей базы	Нет
        regionid 	Коды регионов из нашей базы	Нет
        havecashless 	Есть терминал оплаты (значения 1 - есть, 0 - нет, не указан - оба варианта, отображать все)*	Нет
        isdressingroom 	Есть ли примерочная в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)*	Нет
        weightmax 	Максимальный вес, который можно отправить в ПВЗ (значения больше 0  - показываем ПВЗ которые принимают этот вес, 0 - показываем все, не указан - не показываем ПВЗ с 0 весом).*	Нет
        allowedcod 	Разрешен наложенный платеж в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)*	Нет


        Если одновременно указаны параметры cityid и citypostcode, то для определения города всех стран присутствия СДЭК приоритет отдается cityid. При отсутствии входных параметров список ПВЗ содержит данные по всем городам.

        Параметр ПВЗ используется в документе «Список заказов на доставку» для заказов с режимом  доставки «до склада».
        Можно задать несколько параметров в фильтре через знак &. Если в фильтрах указываются невалидные данные, тогда считаем, что фильтр = 0 и работаем с ним.
        * Допускается вместо цифр указывать true/false.
        Описание получаемых данных:

        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	PvzList	Заголовок документа		да
        2.	Pvz	ПВЗ		да
        2.1.	Code	Код	string(10)	да
        2.2.	Name	Название	string(50)	да
        2.3.	CityCode	Код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        2.4.	City	Название города	string(50)	да
        2.5.	WorkTime	Режим работы, строка вида «пн-пт 9-18, сб 9-16»	string(100)	да
        2.6.	Address	Адрес (улица, дом, офис) в указанном городе	string(255)	да
        2.7.	Phone	Телефон	phone	да
        2.8.	Note	Примечание. Описание местоположения.	string(255)	да
        2.9.	CoordX	Координаты местоположения (долгота) в градусах	float	да
        2.10.	CoordY	Координаты местоположения (широта) в градусах	float	да
        2.11.	WeightLimit	Ограничения по весу для выдачи в ПВЗ (тэг существует только при наличии ограничений)		да
        2.11.1.	WeightMin 	Минимальный вес (в кг.), принимаемый в ПВЗ (> WeightMin)	float	да
        2.11.2.	WeightMax	Максимальный вес (в кг.), принимаемый в ПВЗ (<=WeightMax)	float	да
        2.12.	Type	Тип ПВЗ: Склад СДЭК или Почтомат партнера, PVZ —  склад СДЭК, POSTOMAT — почтомат партнера СДЭК	string(8)	да
        2.13.	OwnerCode	Принадлежность ПВЗ компании: CDEK — ПВЗ принадлежит компании СДЭК, InPost — ПВЗ принадлежит компании InPost.	string(6)	да
        2.14	CountryCode 	Код страны	integer(9)	да
        2.15	CountryName 	Название страны	string(50)	да
        2.16	RegionCode 	Код региона	integer(9)	да
        2.17	RegionName 	Название региона	string(50) 	да
        2.18	FullAddress 	 Полный адрес с указанием страны, региона, города, и т.д.	string(255) 	да
        2.19	IsDressingRoom 	Есть ли примерочная	string(4)	да
        2.20	HaveCashless 	Есть терминал оплаты	string(4)	да
        2.21	NearestStation 	Ближайшая станция/остановка транспорта 	string(50)	да
        2.22	Site 	Сайт пвз на странице СДЭК	string(255)	да
        2.23	MetroStation 	Ближайшая станция метро	string(50)	да
        2.24	WorkTimeY 	график работы на неделю. Вложенный тег с атрибутами day и period.		да
        2.24.1	day 	Порядковый номер дня начиная с единицы. Понедельник = 1, воскресенье = 7.	integer (1)	да
        2.24.2	periods 	Период работы в эти дни.
        Если в этот день не работают, то не отображать.
        <WorkTimeY day="1" periods="10:00/20:00"/>
        <WorkTimeY day="2" periods="10:00/20:00"/>	string(20) 	да
        2.25	OfficeImage 	Все фото офиса (кроме фото как доехать).
        2.25.1	url 	Все фотографии отдельным тегом с атрибутом url. Отображается ссылка на картинку.
        <OfficeImage url="http://dfdfdf/images/22/47_1_SUR2"/>	string(255)	да
        2.26	OfficeHowGo 	Картинка с указанием как доехать		да
        2.26.1	url	Картинка с изображением схемы проезда. Отображается ссылка на картинку.
        <OfficeHowGo url="http://dfdfdf/images/7/42_1_NSK1"/>	string(255)	да


        Для примеров используем сityid=44 — код города Москва, сitypostcode=656065 — почтовый индекс города Барнаула.
        https://integration.cdek.ru/pvzlist.php — в результате получим список всех активных ПВЗ СДЭК на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?type=ALL — в результате получим список всех активных ПВЗ СДЭК и почтоматов на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?cityid=44&type=ALL — в результате получим список всех активных ПВЗ СДЭК и почтоматов в г. Москва на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?cityid=44&type=PVZ — в результате получим список всех активных ПВЗ СДЭК в г. Москва на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?citypostcode=656065&type=POSTOMAT — в результате получим список всех активных почтоматов в г. Барнаул на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?cityid=44&citypostcode=656065 — в результате получим список всех  активных ПВЗ СДЭК в г. Москва на дату формирования запроса.

        Аналогичным образом можно задать фильтры по следующим параметрам:
        countryid, regionid - коды из нашей базы,
        havecashless - есть терминал оплаты (значения 1 - есть, 0 - нет, не указан - оба варианта, отображать все)
        allowedcod - разрешен наложенный платеж в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)
        isdressingroom - есть ли примерочная в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)
        weightmax - максимальный вес, который можно отправить в ПВЗ (значения больше 0  - показываем ПВЗ которые принимают этот вес, 0 - показываем все, не указан - не показываем ПВЗ с 0 весом).

        Документ содержит список ПВЗ в городе Москва (не все, а выборочно для просмотра значений различных атрибутов).
        Т.е. часть ответа при запросе https://integration.cdek.ru/pvzlist.php?cityid=44&type=ALL
        <?xml version="1.0" encoding="UTF-8"?>
        <PvzList weightMax="1">
            <Pvz Code="MSK64" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="Москва" CityCode="44" CityName="Москва" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="есть" HaveCashless="нет" AllowedCod="есть" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
                <WorkTimeY day="1" periods="09:00/20:00" />
                <WorkTimeY day="2" periods="09:00/20:00" />
                <WorkTimeY day="3" periods="09:00/20:00" />
                <WorkTimeY day="4" periods="09:00/20:00" />
                <WorkTimeY day="5" periods="09:00/20:00" />
                <WorkTimeY day="6" periods="09:00/20:00" />
                <WorkTimeY day="7" periods="09:00/20:00" />
                <WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
            </Pvz>
            <Pvz Code="MSK65" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="Москва" CityCode="44" CityName="Москва" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="нет" HaveCashless="нет" AllowedCod="нет" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
                <WorkTimeY day="1" periods="09:00/20:00" />
                <WorkTimeY day="2" periods="09:00/20:00" />
                <WorkTimeY day="3" periods="09:00/20:00" />
                <WorkTimeY day="4" periods="09:00/20:00" />
                <WorkTimeY day="5" periods="09:00/20:00" />
                <WorkTimeY day="6" periods="09:00/20:00" />
                <WorkTimeY day="7" periods="09:00/20:00" />
                <WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
            </Pvz>
            <Pvz Code="MSK71" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="Москва" CityCode="44" CityName="Москва" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="нет" HaveCashless="нет" AllowedCod="нет" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
                <WorkTimeY day="1" periods="09:00/20:00" />
                <WorkTimeY day="2" periods="09:00/20:00" />
                <WorkTimeY day="3" periods="09:00/20:00" />
                <WorkTimeY day="4" periods="09:00/20:00" />
                <WorkTimeY day="5" periods="09:00/20:00" />
                <WorkTimeY day="6" periods="09:00/20:00" />
                <WorkTimeY day="7" periods="09:00/20:00" />
                <WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
            </Pvz>
        </PvzList>


        Параметры  WeightLimit показывает груз, какого веса можно выдать с выбранного ПВЗ, если вес груза больше указанного ограничения, то заказ не будет принят. Например, для ПВЗ «На Авиатмоторной» ограничение от 0 кг до 0 кг, т. е. выдача грузов на данном ПВЗ не возможна.
        Параметры Type="POSTOMAT" и ownerCode="InPost" показывают, что для использования этого почтомата необходимо использовать отдельную услугу, работающую с почтоматами указанного партнера.
        Значение атрибута Code  - используется для создания заказа с режимом доставки «до склада».

        Рекомендуем в ИС ИМ выводить название, адрес, телефон и режим работы ПВЗ. А так же отфильтровать ПВЗ в соответствии с весом груза и ограничением на ПВЗ. Для удобства также можно выводить информацию из поля «Note» – примечание. Дополнительно по координатам можно  строить карты с использованием, например, API яндекс карт.
        */

    }
    public function parcelShopsList($cityName='', $regionName='', $weight=0, $size=0, $payment='', $number='') { //Получение списка точек самовывоза

        $namespace = __NAMESPACE__;

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        if(empty($parcelShopsList)) {
            /**** Memcache ****/

            $pvzlist = $this->authorization->query("{}", "pvzlist.php", "GET", "xml");
            if(isset($pvzlist['error'])) return ['error' => $pvzlist['error']];

            if(!$pvzlist) {

                $answer['error']['code'] = "_1";
                $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

                return $answer;

            }
            if(!is_array($pvzlist)) {

                $answer['error']['code'] = "_2";
                $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                $answer['error']['info'][] = print_r($pvzlist, true);

                return $answer;

            }


            if($pvzlist['Pvz']['@attributes']) $pvzlist['Pvz'] = [$pvzlist['Pvz']];

            foreach($pvzlist['Pvz'] as $pvzId => $pvz) {

                $parcelShopsList[$pvzId]['CitiName'] = $pvz['@attributes']['City'];
                $parcelShopsList[$pvzId]['Region'] = $pvz['@attributes']['RegionName'];
                $parcelShopsList[$pvzId]['Address'] = $pvz['@attributes']['FullAddress'];
                $parcelShopsList[$pvzId]['Latitude'] = $pvz['@attributes']['coordY'];
                $parcelShopsList[$pvzId]['Longitude'] = $pvz['@attributes']['coordX'];
                $parcelShopsList[$pvzId]['Number'] = $pvz['@attributes']['Code'];
                $parcelShopsList[$pvzId]['name'] = $pvz['@attributes']['name'];
                if($pvz['@attributes']['ownerCode']) $parcelShopsList[$pvzId]['OwnerName'] = $pvz['@attributes']['ownerCode'];
                if($pvz['@attributes']['WorkTime']) $parcelShopsList[$pvzId]['WorkTime'] = $pvz['@attributes']['WorkTime'];
                if($pvz['@attributes']['Phone']) $parcelShopsList[$pvzId]['Phone'] = $pvz['@attributes']['Phone'];
                if($pvz['@attributes']['Note']) $parcelShopsList[$pvzId]['Phone'] = $pvz['@attributes']['Note'];
                if($pvz['WeightLimit']) {
                    $parcelShopsList[$pvzId]['MaxWeight'] = $pvz['WeightLimit']['@attributes']['WeightMax'];
                    if($pvz['WeightLimit']['@attributes']['SizeMax']) $parcelShopsList[$pvzId]['MaxSize'] = $pvz['WeightLimit']['@attributes']['SizeMax'];
                }
                if(!$parcelShopsList[$pvzId]['MaxWeight']) $parcelShopsList[$pvzId]['MaxWeight'] = 29;
                if(!$parcelShopsList[$pvzId]['MaxSize']) $parcelShopsList[$pvzId]['MaxSize'] = 450;
                $parcelShopsList[$pvzId]['TypeTitle'] = ($pvz['@attributes']['Type'] == "PVZ") ? "ПВЗ" : "АПТ";
                $parcelShopsList[$pvzId]['Fitting'] = ($pvz['@attributes']['IsDressingRoom'] == "есть") ? true : false;
                $parcelShopsList[$pvzId]['Cash'] = true;
                $parcelShopsList[$pvzId]['Card'] = ($pvz['@attributes']['HaveCashless'] == "нет") ? false : true;
                $parcelShopsList[$pvzId]['Status'] = 2;

            }


            $cachetime = ($this->memcache instanceof \memcache) ? 0 : time() + 60 * 60;

            if($this->memcache) $this->memcache->set($cacheKey, $parcelShopsList, $cachetime);

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $parcelShopsListReturn;

        /*
        Список пунктов выдачи заказов (ПВЗ СДЭК и почтоматы партнеров)
        Документ «Список пунктов выдачи заказов» предоставляет список ПВЗ, действующих на момент запроса.
        Для использования необходимо отправить GET запрос на URL: <сервер>/pvzlist.php, например, https://integration.cdek.ru/pvzlist.php, с заполненными переменными $_GET['cityid'], $_GET['citypostcode' ], $_GET['type' ].
        Описание передаваемых данных:
        Параметр	Описание	Обязательность
        cityid	Код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls», где XXX – трехбуквенный код страны)	Нет
        citypostcode	Почтовый индекс города, для которого необходим список ПВЗ	Нет
        type	Тип пункта выдачи (ПВЗ или почтомат).
        Может принимать значения:
        «PVZ» - для отображения только складов СДЭК.
        «POSTOMAT» - для отображения почтоматов партнёра.
         «ALL» - для отображения всех ПВЗ не зависимо от их типа.
        При отсутствии параметра принимается значение по умолчанию «PVZ» для поддержания работы старых алгоритмов.	Нет
        countryid	Коды стран из нашей базы	Нет
        regionid 	Коды регионов из нашей базы	Нет
        havecashless 	Есть терминал оплаты (значения 1 - есть, 0 - нет, не указан - оба варианта, отображать все)*	Нет
        isdressingroom 	Есть ли примерочная в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)*	Нет
        weightmax 	Максимальный вес, который можно отправить в ПВЗ (значения больше 0  - показываем ПВЗ которые принимают этот вес, 0 - показываем все, не указан - не показываем ПВЗ с 0 весом).*	Нет
        allowedcod 	Разрешен наложенный платеж в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)*	Нет


        Если одновременно указаны параметры cityid и citypostcode, то для определения города всех стран присутствия СДЭК приоритет отдается cityid. При отсутствии входных параметров список ПВЗ содержит данные по всем городам.

        Параметр ПВЗ используется в документе «Список заказов на доставку» для заказов с режимом  доставки «до склада».
        Можно задать несколько параметров в фильтре через знак &. Если в фильтрах указываются невалидные данные, тогда считаем, что фильтр = 0 и работаем с ним.
        * Допускается вместо цифр указывать true/false.
        Описание получаемых данных:

        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	PvzList	Заголовок документа		да
        2.	Pvz	ПВЗ		да
        2.1.	Code	Код	string(10)	да
        2.2.	Name	Название	string(50)	да
        2.3.	CityCode	Код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        2.4.	City	Название города	string(50)	да
        2.5.	WorkTime	Режим работы, строка вида «пн-пт 9-18, сб 9-16»	string(100)	да
        2.6.	Address	Адрес (улица, дом, офис) в указанном городе	string(255)	да
        2.7.	Phone	Телефон	phone	да
        2.8.	Note	Примечание. Описание местоположения.	string(255)	да
        2.9.	CoordX	Координаты местоположения (долгота) в градусах	float	да
        2.10.	CoordY	Координаты местоположения (широта) в градусах	float	да
        2.11.	WeightLimit	Ограничения по весу для выдачи в ПВЗ (тэг существует только при наличии ограничений)		да
        2.11.1.	WeightMin 	Минимальный вес (в кг.), принимаемый в ПВЗ (> WeightMin)	float	да
        2.11.2.	WeightMax	Максимальный вес (в кг.), принимаемый в ПВЗ (<=WeightMax)	float	да
        2.12.	Type	Тип ПВЗ: Склад СДЭК или Почтомат партнера, PVZ —  склад СДЭК, POSTOMAT — почтомат партнера СДЭК	string(8)	да
        2.13.	OwnerCode	Принадлежность ПВЗ компании: CDEK — ПВЗ принадлежит компании СДЭК, InPost — ПВЗ принадлежит компании InPost.	string(6)	да
        2.14	CountryCode 	Код страны	integer(9)	да
        2.15	CountryName 	Название страны	string(50)	да
        2.16	RegionCode 	Код региона	integer(9)	да
        2.17	RegionName 	Название региона	string(50) 	да
        2.18	FullAddress 	 Полный адрес с указанием страны, региона, города, и т.д.	string(255) 	да
        2.19	IsDressingRoom 	Есть ли примерочная	string(4)	да
        2.20	HaveCashless 	Есть терминал оплаты	string(4)	да
        2.21	NearestStation 	Ближайшая станция/остановка транспорта 	string(50)	да
        2.22	Site 	Сайт пвз на странице СДЭК	string(255)	да
        2.23	MetroStation 	Ближайшая станция метро	string(50)	да
        2.24	WorkTimeY 	график работы на неделю. Вложенный тег с атрибутами day и period.		да
        2.24.1	day 	Порядковый номер дня начиная с единицы. Понедельник = 1, воскресенье = 7.	integer (1)	да
        2.24.2	periods 	Период работы в эти дни.
        Если в этот день не работают, то не отображать.
        <WorkTimeY day="1" periods="10:00/20:00"/>
        <WorkTimeY day="2" periods="10:00/20:00"/>	string(20) 	да
        2.25	OfficeImage 	Все фото офиса (кроме фото как доехать).
        2.25.1	url 	Все фотографии отдельным тегом с атрибутом url. Отображается ссылка на картинку.
        <OfficeImage url="http://dfdfdf/images/22/47_1_SUR2"/>	string(255)	да
        2.26	OfficeHowGo 	Картинка с указанием как доехать		да
        2.26.1	url	Картинка с изображением схемы проезда. Отображается ссылка на картинку.
        <OfficeHowGo url="http://dfdfdf/images/7/42_1_NSK1"/>	string(255)	да


        Для примеров используем сityid=44 — код города Москва, сitypostcode=656065 — почтовый индекс города Барнаула.
        https://integration.cdek.ru/pvzlist.php — в результате получим список всех активных ПВЗ СДЭК на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?type=ALL — в результате получим список всех активных ПВЗ СДЭК и почтоматов на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?cityid=44&type=ALL — в результате получим список всех активных ПВЗ СДЭК и почтоматов в г. Москва на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?cityid=44&type=PVZ — в результате получим список всех активных ПВЗ СДЭК в г. Москва на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?citypostcode=656065&type=POSTOMAT — в результате получим список всех активных почтоматов в г. Барнаул на дату формирования запроса.
        https://integration.cdek.ru/pvzlist.php?cityid=44&citypostcode=656065 — в результате получим список всех  активных ПВЗ СДЭК в г. Москва на дату формирования запроса.

        Аналогичным образом можно задать фильтры по следующим параметрам:
        countryid, regionid - коды из нашей базы,
        havecashless - есть терминал оплаты (значения 1 - есть, 0 - нет, не указан - оба варианта, отображать все)
        allowedcod - разрешен наложенный платеж в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)
        isdressingroom - есть ли примерочная в ПВЗ (значения 1 - есть, 0 - нет, не указан - оба варианта)
        weightmax - максимальный вес, который можно отправить в ПВЗ (значения больше 0  - показываем ПВЗ которые принимают этот вес, 0 - показываем все, не указан - не показываем ПВЗ с 0 весом).

        Документ содержит список ПВЗ в городе Москва (не все, а выборочно для просмотра значений различных атрибутов).
        Т.е. часть ответа при запросе https://integration.cdek.ru/pvzlist.php?cityid=44&type=ALL
        <?xml version="1.0" encoding="UTF-8"?>
        <PvzList weightMax="1">
            <Pvz Code="MSK64" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="Москва" CityCode="44" CityName="Москва" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="есть" HaveCashless="нет" AllowedCod="есть" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
                <WorkTimeY day="1" periods="09:00/20:00" />
                <WorkTimeY day="2" periods="09:00/20:00" />
                <WorkTimeY day="3" periods="09:00/20:00" />
                <WorkTimeY day="4" periods="09:00/20:00" />
                <WorkTimeY day="5" periods="09:00/20:00" />
                <WorkTimeY day="6" periods="09:00/20:00" />
                <WorkTimeY day="7" periods="09:00/20:00" />
                <WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
            </Pvz>
            <Pvz Code="MSK65" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="Москва" CityCode="44" CityName="Москва" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="нет" HaveCashless="нет" AllowedCod="нет" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
                <WorkTimeY day="1" periods="09:00/20:00" />
                <WorkTimeY day="2" periods="09:00/20:00" />
                <WorkTimeY day="3" periods="09:00/20:00" />
                <WorkTimeY day="4" periods="09:00/20:00" />
                <WorkTimeY day="5" periods="09:00/20:00" />
                <WorkTimeY day="6" periods="09:00/20:00" />
                <WorkTimeY day="7" periods="09:00/20:00" />
                <WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
            </Pvz>
            <Pvz Code="MSK71" Name="На Вольсковой" CountryCode="1" CountryName="Россия" RegionCode="81" RegionName="Москва" CityCode="44" CityName="Москва" WorkTime="пн-вс 09:00-20:00" Address="ВОЛЬСКАЯ УЛ, 71/42" FullAddress="Россия, Москва, Москва, ВОЛЬСКАЯ УЛ, д.71/42, 1 этаж" Phone="78462500401" Note="вход со стороны ул. Вольская" coordX="50.2406578" coordY="53.2177467" Type="PVZ" ownerCode="cdek" IsDressingRoom="нет" HaveCashless="нет" AllowedCod="нет" NearestStation="Sibirskaya" MetroStation=" " Site="https://dfdfdf.com" >
                <WorkTimeY day="1" periods="09:00/20:00" />
                <WorkTimeY day="2" periods="09:00/20:00" />
                <WorkTimeY day="3" periods="09:00/20:00" />
                <WorkTimeY day="4" periods="09:00/20:00" />
                <WorkTimeY day="5" periods="09:00/20:00" />
                <WorkTimeY day="6" periods="09:00/20:00" />
                <WorkTimeY day="7" periods="09:00/20:00" />
                <WeightLimit WeightMin="1" WeightMax="30"></WeightLimit>
            </Pvz>
        </PvzList>


        Параметры  WeightLimit показывает груз, какого веса можно выдать с выбранного ПВЗ, если вес груза больше указанного ограничения, то заказ не будет принят. Например, для ПВЗ «На Авиатмоторной» ограничение от 0 кг до 0 кг, т. е. выдача грузов на данном ПВЗ не возможна.
        Параметры Type="POSTOMAT" и ownerCode="InPost" показывают, что для использования этого почтомата необходимо использовать отдельную услугу, работающую с почтоматами указанного партнера.
        Значение атрибута Code  - используется для создания заказа с режимом доставки «до склада».

        Рекомендуем в ИС ИМ выводить название, адрес, телефон и режим работы ПВЗ. А так же отфильтровать ПВЗ в соответствии с весом груза и ограничением на ПВЗ. Для удобства также можно выводить информацию из поля «Note» – примечание. Дополнительно по координатам можно  строить карты с использованием, например, API яндекс карт.
        */

    }
    public function shippingPointsList() { //Получение списка точек сдачи
    }
    public function tariffList() { //Информация о тарифах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $tariffList[0]['id'] = "136";
        $tariffList[0]['name'] = "Доставка до ПВЗ";
        $tariffList[0]['mode'] = "Д-С";
        $tariffList[0]['confines']['weight'] = "30";
        $tariffList[0]['type'] = "Посылка";
        $tariffList[0]['description'] = "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";

        $tariffList[1]['id'] = "137";
        $tariffList[1]['name'] = "Доставка до двери";
        $tariffList[1]['mode'] = "Д-Д";
        $tariffList[1]['confines']['weight'] = "30";
        $tariffList[1]['type'] = "Посылка";
        $tariffList[1]['description'] = "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";

        /*$tariffList[2]['id'] = "138";
        $tariffList[2]['name'] = "Посылка дверь-склад";
        $tariffList[2]['mode'] = "Д-С";
        $tariffList[2]['confines']['weight'] = "30";
        $tariffList[2]['type'] = "Посылка";
        $tariffList[2]['description'] = "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";

        $tariffList[3]['id'] = "139";
        $tariffList[3]['name'] = "Посылка дверь-дверь";
        $tariffList[3]['mode'] = "Д-Д";
        $tariffList[3]['confines']['weight'] = "30";
        $tariffList[3]['type'] = "Посылка";
        $tariffList[3]['description'] = "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";

        $tariffList[4]['id'] = "233";
        $tariffList[4]['name'] = "Экономичная посылка склад-дверь";
        $tariffList[4]['mode'] = "С-Д";
        $tariffList[4]['confines']['weight'] = "50";
        $tariffList[4]['type'] = "Экономичная посылка";
        $tariffList[4]['description'] = "Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.";

        $tariffList[5]['id'] = "234";
        $tariffList[5]['name'] = "Экономичная посылка склад-склад";
        $tariffList[5]['mode'] = "С-С";
        $tariffList[5]['confines']['weight'] = "50";
        $tariffList[5]['type'] = "Экономичная посылка";
        $tariffList[5]['description'] = "Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.";

        $tariffList[6]['id'] = "301";
        $tariffList[6]['name'] = "До постомата InPost дверь-склад";
        $tariffList[6]['mode'] = "Д-С";
        $tariffList[6]['confines']['weight'] = "20";
        $tariffList[6]['confines']['length'] = "41";
        $tariffList[6]['confines']['depth'] = "38";
        $tariffList[6]['confines']['width'] = "64";
        $tariffList[6]['type'] = "InPost";
        $tariffList[6]['description'] = "Услуга доставки товаров по России с использованием постоматов. Для компаний, осуществляющих дистанционную торговлю.
        Характеристики услуги:
        - по услуге принимаются только одноместные заказы
        - выбранный при оформлении заказа постомат изменить на другой нельзя
        - при невозможности использования постоматов осуществляется доставка до ПВЗ СДЭК или «до двери» клиента с изменением тарификации на услугу «Посылка»
        - срок хранения заказа в ячейке: 5 дней с момента закладки в постомат
        - возможность приема наложенного платежа
        ";

        $tariffList[7]['id'] = "302";
        $tariffList[7]['name'] = "До постомата InPost склад-склад";
        $tariffList[7]['mode'] = "С-С";
        $tariffList[7]['confines']['weight'] = "20";
        $tariffList[7]['confines']['length'] = "41";
        $tariffList[7]['confines']['depth'] = "38";
        $tariffList[7]['confines']['width'] = "64";
        $tariffList[7]['type'] = "InPost";
        $tariffList[7]['description'] = "Услуга доставки товаров по России с использованием постоматов. Для компаний, осуществляющих дистанционную торговлю.
        Характеристики услуги:
        - по услуге принимаются только одноместные заказы
        - выбранный при оформлении заказа постомат изменить на другой нельзя
        - при невозможности использования постоматов осуществляется доставка до ПВЗ СДЭК или «до двери» клиента с изменением тарификации на услугу «Посылка»
        - срок хранения заказа в ячейке: 5 дней с момента закладки в постомат
        - возможность приема наложенного платежа
        ";

        $tariffList[8]['id'] = "291";
        $tariffList[8]['name'] = "CDEK Express склад-склад";
        $tariffList[8]['mode'] = "С-С";
        $tariffList[8]['type'] = "CDEK Express";
        $tariffList[8]['description'] = "Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        ";

        $tariffList[9]['id'] = "293";
        $tariffList[9]['name'] = "CDEK Express дверь-дверь";
        $tariffList[9]['mode'] = "Д-Д";
        $tariffList[9]['type'] = "CDEK Express";
        $tariffList[9]['description'] = "Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        ";

        $tariffList[10]['id'] = "294";
        $tariffList[10]['name'] = "CDEK Express склад-дверь";
        $tariffList[10]['mode'] = "С-Д";
        $tariffList[10]['type'] = "CDEK Express";
        $tariffList[10]['description'] = "Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        ";

        $tariffList[11]['id'] = "295";
        $tariffList[11]['name'] = "CDEK Express дверь-склад";
        $tariffList[11]['mode'] = "Д-С";
        $tariffList[11]['type'] = "CDEK Express";
        $tariffList[11]['description'] = "Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        Сервис по доставке товаров из-за рубежа в России с услугами по таможенному оформлению.
        Три варианта предоставления услуги:
        1) Мы забираем из другой страны, ввозим в РФ, проходим таможню, доставляем - накладная оформляется, например как Пекин-Новосибирск
        2) Клиент сам ввозит груз в Россию, мы проходим российскую таможню и доставляем - накладная оформляется, например как Москва-Новосибирск
        3) Клиент сам ввозит груз в Россию, сам проходит российскую таможню, мы только доставляем - накладная оформляется, например как Москва-Новосибирск
        ";

        $tariffList[12]['id'] = "1";
        $tariffList[12]['name'] = "Экспресс лайт дверь-дверь";
        $tariffList[12]['mode'] = "Д-Д";
        $tariffList[12]['type'] = "Экспресс";
        $tariffList[12]['confines']['weight'] = "30";
        $tariffList[12]['description'] = "Классическая экспресс-доставка по России документов и грузов до 30 кг.";

        $tariffList[13]['id'] = "3";
        $tariffList[13]['name'] = "Супер-экспресс до 18";
        $tariffList[13]['mode'] = "Д-Д";
        $tariffList[13]['type'] = "Срочная доставка";
        $tariffList[13]['description'] = "Срочная доставка документов и грузов «из рук в руки» по России к определенному часу.";

        $tariffList[14]['id'] = "5";
        $tariffList[14]['name'] = "Экономичный экспресс склад-склад";
        $tariffList[14]['mode'] = "С-С";
        $tariffList[14]['type'] = "Экономичная доставка";
        $tariffList[14]['description'] = "Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).";

        $tariffList[15]['id'] = "10";
        $tariffList[15]['name'] = "Экспресс лайт склад-склад";
        $tariffList[15]['mode'] = "С-С";
        $tariffList[15]['type'] = "Экспресс";
        $tariffList[12]['confines']['weight'] = "30";
        $tariffList[15]['description'] = "Классическая экспресс-доставка по России документов и грузов.";

        $tariffList[16]['id'] = "11";
        $tariffList[16]['name'] = "Экспресс лайт склад-дверь";
        $tariffList[16]['mode'] = "С-Д";
        $tariffList[16]['type'] = "Экспресс";
        $tariffList[16]['confines']['weight'] = "30";
        $tariffList[16]['description'] = "Классическая экспресс-доставка по России документов и грузов.";

        $tariffList[17]['id'] = "12";
        $tariffList[17]['name'] = "Экспресс лайт дверь-склад";
        $tariffList[17]['mode'] = "Д-С";
        $tariffList[17]['type'] = "Экспресс";
        $tariffList[17]['confines']['weight'] = "30";
        $tariffList[17]['description'] = "Классическая экспресс-доставка по России документов и грузов.";

        $tariffList[18]['id'] = "17";
        $tariffList[18]['name'] = "Экспресс тяжеловесы дверь-склад";
        $tariffList[18]['mode'] = "Д-С";
        $tariffList[18]['type'] = "Экспресс";
        $tariffList[18]['confines']['minWeight'] = "30";
        $tariffList[18]['description'] = "Классическая экспресс-доставка по России грузов.";

        $tariffList[19]['id'] = "18";
        $tariffList[19]['name'] = "Экспресс тяжеловесы дверь-дверь";
        $tariffList[19]['mode'] = "Д-Д";
        $tariffList[19]['type'] = "Экспресс";
        $tariffList[19]['confines']['minWeight'] = "30";
        $tariffList[19]['description'] = "Классическая экспресс-доставка по России грузов.";

        $tariffList[20]['id'] = "15";
        $tariffList[20]['name'] = "Экспресс тяжеловесы склад-склад";
        $tariffList[20]['mode'] = "С-С";
        $tariffList[20]['type'] = "Экспресс";
        $tariffList[20]['confines']['minWeight'] = "30";
        $tariffList[20]['description'] = "Классическая экспресс-доставка по России грузов.";

        $tariffList[21]['id'] = "16";
        $tariffList[21]['name'] = "Экспресс тяжеловесы склад-дверь";
        $tariffList[21]['mode'] = "С-Д";
        $tariffList[21]['type'] = "Экспресс";
        $tariffList[21]['confines']['minWeight'] = "30";
        $tariffList[21]['description'] = "Классическая экспресс-доставка по России грузов.";

        $tariffList[22]['id'] = "57";
        $tariffList[22]['name'] = "Супер-экспресс до 9";
        $tariffList[22]['mode'] = "Д-Д";
        $tariffList[22]['type'] = "Срочная доставка";
        $tariffList[22]['confines']['weight'] = "5";
        $tariffList[22]['description'] = "Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).";

        $tariffList[23]['id'] = "58";
        $tariffList[23]['name'] = "Супер-экспресс до 10";
        $tariffList[23]['mode'] = "Д-Д";
        $tariffList[23]['type'] = "Срочная доставка";
        $tariffList[23]['confines']['weight'] = "5";
        $tariffList[23]['description'] = "Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).";

        $tariffList[24]['id'] = "59";
        $tariffList[24]['name'] = "Супер-экспресс до 12";
        $tariffList[24]['mode'] = "Д-Д";
        $tariffList[24]['type'] = "Срочная доставка";
        $tariffList[24]['confines']['weight'] = "5";
        $tariffList[24]['description'] = "Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).";

        $tariffList[25]['id'] = "60";
        $tariffList[25]['name'] = "Супер-экспресс до 14";
        $tariffList[25]['mode'] = "Д-Д";
        $tariffList[25]['type'] = "Срочная доставка";
        $tariffList[25]['confines']['weight'] = "5";
        $tariffList[25]['description'] = "Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).";

        $tariffList[26]['id'] = "61";
        $tariffList[26]['name'] = "Супер-экспресс до 16";
        $tariffList[26]['mode'] = "Д-Д";
        $tariffList[26]['type'] = "Срочная доставка";
        $tariffList[26]['confines']['weight'] = "5";
        $tariffList[26]['description'] = "Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).";

        $tariffList[27]['id'] = "62";
        $tariffList[27]['name'] = "Магистральный экспресс склад-склад";
        $tariffList[27]['mode'] = "С-С";
        $tariffList[27]['type'] = "Экономичная доставка";
        $tariffList[27]['description'] = "Быстрая экономичная доставка грузов по России";

        $tariffList[28]['id'] = "63";
        $tariffList[28]['name'] = "Магистральный супер-экспресс склад-склад";
        $tariffList[28]['mode'] = "С-С";
        $tariffList[28]['type'] = "Экономичная доставка";
        $tariffList[28]['description'] = "Быстрая экономичная доставка грузов к определенному часу";*/


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $tariffList;

    }
    public function errorList() { //Информация об ошибках

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $errors[0]['Error'] = 'ERR_API';
        $errors[0]['ErrorText'] = "Ошибка обращения к API";

        $errors[1]['Error'] = 'ERR_ATTRIBUTE_EMPTY';
        $errors[1]['ErrorText'] = 'Не задано значение атрибута:#var1';

        $errors[2]['Error'] = 'ERR_AUTH';
        $errors[2]['ErrorText'] = 'Интернет-магазин не идентифицирован: Account=#var1, Secure=#var2	Неверная передача значений для аутентификации интернет-магазина: Account, Secure_password. Неверное кодирование значения Secure_password';

        $errors[3]['Error'] = 'ERR_BARCODE_DUBL';
        $errors[3]['ErrorText'] = 'Дублирование BarCode=#var1 в заказе Number=#var2';

        $errors[4]['Error'] = 'ERR_CALLCOURIER_CITY';
        $errors[4]['ErrorText'] = 'Вызов курьера в выбранном городе невозможен:SendCityCode=#var1	"Вызов курьера" в выбранном городе невозможен:#var2';

        $errors[5]['Error'] = 'ERR_CALLCOURIER_COUNT';
        $errors[5]['ErrorText'] = 'Количество передаваемых заявок не соответствует контрольному значению: CallCount=#var1	Количество "Вызовов курьера" не соответствует контрольному значению: CallCount=#var1';

        $errors[6]['Error'] = 'ERR_CALLCOURIER_DATETIME';
        $errors[6]['ErrorText'] = 'Вызов курьера возможно сделать только на будущую дату:Date=#var1	Дата и время вызова курьера может быть только в будущем';

        $errors[7]['Error'] = 'ERR_CALLCOURIER_DATE_DUBL';
        $errors[7]['ErrorText'] = 'Дата вызова курьера дублируется в файле: Date=#var1	Не допускается дублирование дат вызова курьера в файле';

        $errors[8]['Error'] = 'ERR_CALLCOURIER_DATE_EXISTS';
        $errors[8]['ErrorText'] = 'На текущую дату уже есть вызов курьера: Date=#var1	Курьер на выбранную дату уже назначен';

        $errors[9]['Error'] = 'ERR_CALLCOURIER_TIME';
        $errors[9]['ErrorText'] = 'Временной диапазон ожидания приезда курьера указан некорректно: TimeBeg>TimeEnd	Временной диапазон ожидания приезда курьера указан некорректно: TimeBeg>TimeEnd';

        $errors[10]['Error'] = 'ERR_CALLCOURIER_TIMELUNCH';
        $errors[10]['ErrorText'] = 'Временной диапазон обеденного перерыва указан некорректно: LunchBeg>LunchEnd	Временной диапазон обеденного перерыва указан некорректно: LunchBeg>LunchEnd';

        $errors[11]['Error'] = 'ERR_CALLCOURIER_TIME_INTERVAL';
        $errors[11]['ErrorText'] = 'Интервал ожидания курьера между TimeBeg и TimeEnd должен составлять не менее 3 непрерывных часов	Интервал ожидания курьера должен составлять не менее 3 непрерывных часов';

        $errors[12]['Error'] = 'ERR_CALL_DUBL';
        $errors[12]['ErrorText'] = 'Дублирование вызова курьера на одну дату : Date=#var1	В документе «Вызов курьера» на одну дату более одной заявки на вызов курьера. Допускается один приезд курьера в течении дня на один адрес.';

        $errors[13]['Error'] = 'ERR_CASH_LIMIT';
        $errors[13]['ErrorText'] = 'Ограничение суммы наложенного платежа в городе получателя в размере: #var1	СДЭК имеет ограничение по сумме наложенного платежа в городе получателя в размере #var1.';

        $errors[14]['Error'] = 'ERR_CASH_NO';
        $errors[14]['ErrorText'] = 'Получение наложенного платежа в городе невозможно: #var1	СДЭК не имеет возможности получения наложенного платежа в городе получателя.';

        $errors[15]['Error'] = 'ERR_DATABASE';
        $errors[15]['ErrorText'] = 'Ошибка обращения к базе данных СДЭК.	Ошибка обращение к базе данных СДЭК. Необходимо обратиться в поддержку СДЭК';

        $errors[16]['Error'] = 'ERR_DATEFORMAT';
        $errors[16]['ErrorText'] = 'Неверный формат даты в параметре #var1 = #var2';

        $errors[17]['Error'] = 'ERR_DATEINVOICE';
        $errors[17]['ErrorText'] = 'Не указана дата инвойса	Не указана дата инвойса';

        $errors[18]['Error'] = 'ERR_DBO_AUTH';
        $errors[18]['ErrorText'] = 'Ошибка авторизации: неверный логин/пароль';

        $errors[19]['Error'] = 'ERR_DBO_AUTH_HASH';
        $errors[19]['ErrorText'] = 'Ошибка авторизации: неверный хеш или его время жизни истекло, авторизуйтесь снова.';

        $errors[20]['Error'] = 'ERR_DBO_CITY_INCORRECT_FOR_SERVICE';
        $errors[20]['ErrorText'] = 'В указанном городе невозможно осуществить заданную услугу';

        $errors[21]['Error'] = 'ERR_DBO_CLIENT_CURRENCY';
        $errors[21]['ErrorText'] = 'Заданная валюта не соответствует валюте клиента';

        $errors[22]['Error'] = 'ERR_DBO_DATE_CREATE_INVALID';
        $errors[22]['ErrorText'] = 'Дата содания накладной должна быть позже или равна дате создания заявки';

        $errors[23]['Error'] = 'ERR_DBO_FORBIDDEN_SUM_FOR_CASH';
        $errors[23]['ErrorText'] = 'В городе-получаетеле невозможен прием наличных, равный сумме услуг по накладной';

        $errors[24]['Error'] = 'ERR_DBO_INCORRECT_URL';
        $errors[24]['ErrorText'] = 'Неправильный URL';

        $errors[25]['Error'] = 'ERR_DBO_INVALID_ADDITIONALSERVICE';
        $errors[25]['ErrorText'] = 'При заданных условиях невозможно применение выбранной доп. услуги ';

        $errors[26]['Error'] = 'ERR_DBO_NEED_AUTH';
        $errors[26]['ErrorText'] = 'Вы не авторизовались';

        $errors[27]['Error'] = 'ERR_DBO_NOT_FOUND';
        $errors[27]['ErrorText'] = 'Неизвестная ошибка: обратитесь к разработчикам СДЭК для устранения';

        $errors[28]['Error'] = 'ERR_DBO_PACKAGE_NUMBER_EMPTY';
        $errors[28]['ErrorText'] = 'Не задан номер упаковки';

        $errors[29]['Error'] = 'ERR_DBO_PARAM_EMPTY';
        $errors[29]['ErrorText'] = 'Не задано значение атрибута';

        $errors[30]['Error'] = 'ERR_DBO_PARAM_MISTAKE';
        $errors[30]['ErrorText'] = 'Неверно задано значение атрибута';

        $errors[31]['Error'] = 'ERR_DBO_PARAM_MISTAKE_PVZ';
        $errors[31]['ErrorText'] = 'При заданном типе тарифа расчёт по выбранной услуге невозможен, в выбранном городе-получателе нет ПВЗ.';

        $errors[32]['Error'] = 'ERR_DBO_PARAM_SERVICE_INTERFACE';
        $errors[32]['ErrorText'] = 'При заданном значении интерфейса расчёт по выбранной услуге невозможен';

        $errors[33]['Error'] = 'ERR_DBO_PAYER_EMPTY';
        $errors[33]['ErrorText'] = 'Не задан плательщик';

        $errors[34]['Error'] = 'ERR_DBO_RECEIVER_ADDRESS_TITLE_EMPTY';
        $errors[34]['ErrorText'] = 'Не задано строковое представление адреса получателя';

        $errors[35]['Error'] = 'ERR_DBO_RECEIVER_CITY_AMBIGUOUS';
        $errors[35]['ErrorText'] = 'Невозможно однозначно идентифицировать заданный город-получатель';

        $errors[36]['Error'] = 'ERR_DBO_RECEIVER_CITY_EMPTY';
        $errors[36]['ErrorText'] = 'Не задан город-получатель';

        $errors[37]['Error'] = 'ERR_DBO_RECEIVER_CITY_INCORRECT';
        $errors[37]['ErrorText'] = 'Заданный город-получатель не существует в базе СДЭК';

        $errors[38]['Error'] = 'ERR_DBO_RECEIVER_CITY_NOT_INTEGER';
        $errors[38]['ErrorText'] = 'Код города-получателя должен быть целым числом';

        $errors[39]['Error'] = 'ERR_DBO_RESULT_EMPTY';
        $errors[39]['ErrorText'] = 'По данному направлению при заданных условиях нет доступных тарифов';

        $errors[40]['Error'] = 'ERR_DBO_RESULT_SERVICE_EMPTY';
        $errors[40]['ErrorText'] = 'По данному направлению при заданных условиях выбранный тариф недоступен';

        $errors[41]['Error'] = 'ERR_DBO_SENDER_ADDRESS_TITLE_EMPTY';
        $errors[41]['ErrorText'] = 'Не задано строковое представление адреса отправителя';

        $errors[42]['Error'] = 'ERR_DBO_SENDER_CITY_AMBIGUOUS';
        $errors[42]['ErrorText'] = 'Невозможно однозначно идентифицировать заданный город-отправитель';

        $errors[43]['Error'] = 'ERR_DBO_SENDER_CITY_EMPTY';
        $errors[43]['ErrorText'] = 'Не задан город-отправитель';

        $errors[44]['Error'] = 'ERR_DBO_SENDER_CITY_INCORRECT';
        $errors[44]['ErrorText'] = 'Заданный город-отправитель не существует в базе СДЭК';

        $errors[45]['Error'] = 'ERR_DBO_SENDER_CITY_NOT_INTEGER';
        $errors[45]['ErrorText'] = 'Код города-отправителя должен быть целым числом';

        $errors[46]['Error'] = 'ERR_DBO_SERVICE_EMPTY';
        $errors[46]['ErrorText'] = 'Не задана услуга';

        $errors[47]['Error'] = 'ERR_DBO_TYPE_EMPTY';
        $errors[47]['ErrorText'] = 'Не задан тип заказа';

        $errors[48]['Error'] = 'ERR_DBO_WEIGHT_EMPTY';
        $errors[48]['ErrorText'] = 'Не задан вес отправления';

        $errors[49]['Error'] = 'ERR_DELETEREQUEST_ORDER';
        $errors[49]['ErrorText'] = 'Невозможно удалить заказ, т. к. есть движение на складе: Number=#var1	Невозможно удалить заказ, т. к. есть движение на складе: Number=#var1';

        $errors[50]['Error'] = 'ERR_DELETEREQUEST_ORDER_ACTNUMBER';
        $errors[50]['ErrorText'] = 'Заказ принадлежит другому акту приема-передачи: Number=#var1';

        $errors[51]['Error'] = 'ERR_DELETEREQUEST_ORDER_DELETED';
        $errors[51]['ErrorText'] = 'Заказ удален ранее: Number=#var1	Заказ удален ранее: Number=#var1';

        $errors[52]['Error'] = 'ERR_DELETEREQUEST_ORDER_MOVE';
        $errors[52]['ErrorText'] = 'Заказ не в состоянии "Создан": Number=#var1';

        $errors[53]['Error'] = 'ERR_FILE_NOTEXISTS';
        $errors[53]['ErrorText'] = 'Невозможно получить файл. Сервер не отвечает.	Невозможно получить файл. Сервер не отвечает.';

        $errors[54]['Error'] = 'ERR_FILE_SAVE';
        $errors[54]['ErrorText'] = 'Ошибка сохранения файла из #var1	Произошла ошибка при сохранении файла на FTP';

        $errors[55]['Error'] = 'ERR_FIRST_DUBL_EXISTS';
        $errors[55]['ErrorText'] = 'Заявка на вызов курьера существует в базе СДЭК на дату: Date=#var1	В документе «Вызов курьера» заявка на дату уже существует в базе СДЭК:#var1. Необходимо удалить заявку из документа';

        $errors[56]['Error'] = 'ERR_INFOREQUEST';
        $errors[56]['ErrorText'] = 'Недостаточно параметров для формирования отчета. Отсутствуют тэг ChangePeriod или Order.	Недостаточно параметров для формирования отчета Информация по заказам';

        $errors[57]['Error'] = 'ERR_INFOREQUEST_DATEBEG';
        $errors[57]['ErrorText'] = 'Недостаточно параметров для формирования отчета. Отсутствует параметр DateBeg	Недостаточно параметров для формирования отчета Информация по заказам: отсутствует параметр DateBeg';

        $errors[58]['Error'] = 'ERR_INVALID_ADDRESS_DELIVERY';
        $errors[58]['ErrorText'] = 'Неверный адресс доставки: Number=#var1	Неверный адресс доставки';

        $errors[59]['Error'] = 'ERR_INVALID_AMOUNT';
        $errors[59]['ErrorText'] = 'Невалидное значение количества вложений: Amount=#var1	Некорректное значение количества переданных единиц товара: #var1';

        $errors[60]['Error'] = 'ERR_INVALID_COST';
        $errors[60]['ErrorText'] = 'Значение атрибута Cost должно быть больше 0	Значение стоимости единицы товара должно быть неотрицательным: #var1';

        $errors[61]['Error'] = 'ERR_INVALID_COSTEX';
        $errors[61]['ErrorText'] = 'Значение атрибута CostEx должно быть неотрицательным	Значение стоимости единицы товара должно быть неотрицательным: #var1';

        $errors[62]['Error'] = 'ERR_INVALID_DELIVERYRECIPIENTCOST';
        $errors[62]['ErrorText'] = 'Значение доп. сбора за доставку должно быть неотрицательным: DeliveryRecipientCost=#var1	Значение доп. сбора за доставку должно быть неотрицательное значение: #var1';

        $errors[63]['Error'] = 'ERR_INVALID_DISPACHNUMBER';
        $errors[63]['ErrorText'] = 'Заказ не найден в базе СДЭК: DispachNumber=#var1	Невозможно вывести информацию по заказу: ИМ не имеет данный заказ  DispachNumber=#var1';

        $errors[64]['Error'] = 'ERR_INVALID_INTAKESERVICE';
        $errors[64]['ErrorText'] = 'Доп. услуга Забор в городе отправителя не соответствует выбранному тарифу	Доп. услуга Забор в городе отправителя не соответствует выбранному тарифу';

        $errors[65]['Error'] = 'ERR_INVALID_INTAKESERVICE_TOCITY';
        $errors[65]['ErrorText'] = 'Доп. услуга Доставка в городе получателе не соответствует выбранному тарифу	Доп. услуга Доставка в городе получателе не соответствует выбранному тарифу';

        $errors[66]['Error'] = 'ERR_INVALID_NUMBER';
        $errors[66]['ErrorText'] = 'заказ не найден в базе СДЭК: Number=#var1, Date=#var2	Невозможно вывести информацию по заказу: либо заказ не существует, либо принадлежит другому клиенту: Number=#var1, Date=#var2';

        $errors[67]['Error'] = 'ERR_INVALID_NUMBER_DELETE';
        $errors[67]['ErrorText'] = 'заказ не найден в базе СДЭК: Number=#var1	Невозможно вывести информацию по заказу: либо заказ не существует, либо принадлежит другому клиенту: Number=#var1, Date=#var2';

        $errors[68]['Error'] = 'ERR_INVALID_PAYMENT';
        $errors[68]['ErrorText'] = 'Значение атрибута должно быть неотрицательным: Payment	Значение суммы оплаты за единицу товара должно быть неотрицательным: #var1';

        $errors[69]['Error'] = 'ERR_INVALID_PAYMENTEX';
        $errors[69]['ErrorText'] = 'Значение атрибута PaymentEx должно быть неотрицательным	Значение суммы оплаты за единицу товара должно быть неотрицательным: #var1';

        $errors[70]['Error'] = 'ERR_INVALID_SERVICECODE';
        $errors[70]['ErrorText'] = 'Дополнительная услуга не идентифицирована по коду: ServiceCode=#var1	В коде доп. услуги к доставке передано значение, отсутствующее в списке возможных.';

        $errors[71]['Error'] = 'ERR_INVALID_SIZE';
        $errors[71]['ErrorText'] = 'Невалидное значение габаритов: #var1=#var2	Невалидное значение габаритов отправления: #var1=#var2';

        $errors[72]['Error'] = 'ERR_INVALID_TARIFFTYPECODE';
        $errors[72]['ErrorText'] = 'Тариф не идентифицирован по коду: TariffTypeCode=#var1	В коде тарифа на доставку передано значение, отсутствующее в списке возможных. Необходимо обратить к программисту ИМ.';

        $errors[73]['Error'] = 'ERR_INVALID_WEIGHT';
        $errors[73]['ErrorText'] = 'Значение веса должно быть положительным: Weight= #var1	Значение веса отправления должно быть положительным';

        $errors[74]['Error'] = 'ERR_INVALID_XML';
        $errors[74]['ErrorText'] = 'Неверный формат XML. #var1	При неверном тексте формата XML. Возможно в значениях атрибутов есть служебные символы.';

        $errors[75]['Error'] = 'ERR_ITEM_NOTFIND';
        $errors[75]['ErrorText'] = 'Отсутствует вложение в базе СДЭК с артикулом: WareKey=#var1	Отсутствует вложение в базе СДЭК с артикулом: WareKey=#var1';

        $errors[76]['Error'] = 'ERR_NEED_ATTRIBUTE';
        $errors[76]['ErrorText'] = 'Отсутствие обязательного атрибута: #var1	При отсутствии значений обязательных атрибутов передаваемых данных, описанных в регламенте обмена. Необходимо обратиться к программисту ИМ';

        $errors[77]['Error'] = 'ERR_NOTFOUNDCURRENCY';
        $errors[77]['ErrorText'] = 'Код валюты не найден в базе СДЭК:Currency=#var1';

        $errors[78]['Error'] = 'ERR_NOTFOUNDTAG';
        $errors[78]['ErrorText'] = 'Не найден обязательный тег:#var1';

        $errors[79]['Error'] = 'ERR_NOT_EQUAL_CALLCOUNT';
        $errors[79]['ErrorText'] = 'Количество передаваемых заявок на вызов курьера не соответствует контрольному значению: CallCount=#var1	Несоответствие количества фактически переданных данных по заказам и контрольного числа CallCount.Необходимо обратить к программисту ИМ.';

        $errors[80]['Error'] = 'ERR_NOT_EQUAL_ORDERCOUNT';
        $errors[80]['ErrorText'] = 'Количество передаваемых заказов не соответствует контрольному значению: OrderCount=#var1	Несоответствие количества фактически переданных данных по заказам и контрольного числа OrderCount.Необходимо обратить к программисту ИМ.';

        $errors[81]['Error'] = 'ERR_NOT_FOUND_COSTEX';
        $errors[81]['ErrorText'] = 'Не найдено значение атрибута COSTEX	Не найдено значение атрибута COSTEX';

        $errors[82]['Error'] = 'ERR_NOT_FOUND_PASSPORTNUMBER';
        $errors[82]['ErrorText'] = 'Не найдены паспортные данные получателя	Не найдены паспортные данные получателя';

        $errors[83]['Error'] = 'ERR_NOT_FOUND_PAYMENTEX';
        $errors[83]['ErrorText'] = 'Не найдено значение атрибута PAYMENTEX	Не найдено значение атрибута PAYMENTEX';

        $errors[84]['Error'] = 'ERR_NOT_FOUND_RECCITY';
        $errors[84]['ErrorText'] = 'Отсутствуют параметры для идентификации города получателя: RecCityCode, RecCityPostCode	В передаваемых данных нет хотя бы одного атрибута для идентификации города (код RecCityCode или почтовый индекс RecCityPostCode)';

        $errors[85]['Error'] = 'ERR_NOT_FOUND_REGISTRATIONADDRESS';
        $errors[85]['ErrorText'] = 'Не найден адрес регистрации получателя	Не найден адрес регистрации получателя';

        $errors[86]['Error'] = 'ERR_NOT_FOUND_SENDCITY';
        $errors[86]['ErrorText'] = 'Отсутствуют параметры для идентификации города отправителя: SendCityCode, SendCityPostCode	В передаваемых данных нет хотя бы одного атрибута для идентификации города (код SendCityCode или почтовый индекс SendCityPostCode)';

        $errors[87]['Error'] = 'ERR_NOT_FOUND_TARIFFTYPECODE';
        $errors[87]['ErrorText'] = 'Тариф по переданному направлению не существует: TariffTypeCode=#var1	В базе СДЭК не найден тариф по направлению. Возможно указан неверный режим доставки, либо тариф отсутствует. Необходимо обратиться к менеджеру по сопровождению.';

        $errors[88]['Error'] = 'ERR_NOT_VALID_PASSPORTNUMBER';
        $errors[88]['ErrorText'] = 'Неверные паспортные данные получателя	Неверные паспортные данные получателя';

        $errors[89]['Error'] = 'ERR_ORDER_COUNT';
        $errors[89]['ErrorText'] = 'Превышено ограничение количества заказов в одном запросе для формирования файла  (max=100)	Превышено ограничение количества заказов в одном запросе для формирования файла  (max=100)';

        $errors[90]['Error'] = 'ERR_ORDER_DELETE';
        $errors[90]['ErrorText'] = 'Невозможно удалить заказ: Number=#var	Невозможно удалить заказ, т. к. есть движение на складе, или удален ранее, или не найден в базе СДЭК';

        $errors[91]['Error'] = 'ERR_ORDER_DUBL';
        $errors[91]['ErrorText'] = 'Дублирование заказа в документе: Number=#var1	В документе «Список заказов на доставку» данные по одному номеру заказа дублируются: #var1. Необходимо обратиться к программисту ИМ';

        $errors[92]['Error'] = 'ERR_ORDER_DUBL_EXISTS';
        $errors[92]['ErrorText'] = 'Заказ существует в базе СДЭК: Number=#var1	В документе «Список заказов на доставку» передается информация по заказу, который уже существует в базе СДЭК: #var1. Необходимо удалить заказ из документа';

        $errors[93]['Error'] = 'ERR_ORDER_NOTFIND';
        $errors[93]['ErrorText'] = 'Заказ не найден в базе СДЭК: Number=#var1';

        $errors[94]['Error'] = 'ERR_PACKAGE_NOTFIND';
        $errors[94]['ErrorText'] = 'Отсутствует упаковка в базе СДЭК: Number=#var1	Отсутствует упаковка в базе СДЭК: Number=#var1';

        $errors[95]['Error'] = 'ERR_PACKAGE_NUM_DUBL';
        $errors[95]['ErrorText'] = 'Дублирование номера упаковки=#var1 в заказе Number=#var2';

        $errors[96]['Error'] = 'ERR_PRINT_ORDER';
        $errors[96]['ErrorText'] = 'Не удалось сформировать файл печатной формы.';

        $errors[97]['Error'] = 'ERR_PVZCODE';
        $errors[97]['ErrorText'] = 'Код ПВЗ отсутствует в базе СДЭК: PvzCode=#var1';

        $errors[98]['Error'] = 'ERR_PVZCODE_NOTFOUND';
        $errors[98]['ErrorText'] = 'ПВЗ не найден в городе #var1: PvzCode=#var2';

        $errors[99]['Error'] = 'ERR_PVZ_CLOSED';
        $errors[99]['ErrorText'] = 'Указанный ПВЗ: #var1 на данный момент закрыт. Выберите из работающих: #var2';

        $errors[100]['Error'] = 'ERR_PVZ_NOTFOUND';
        $errors[100]['ErrorText'] = 'В выбранном городе ПВЗ отсутствуют: cityid=#var1';

        $errors[101]['Error'] = 'ERR_PVZ_NOTFOUND_BY_POSTCODE';
        $errors[101]['ErrorText'] = 'По выбранному индексу ПВЗ отсутствуют: PostCode=#var1';

        $errors[102]['Error'] = 'ERR_PVZ_WEIGHT';
        $errors[102]['ErrorText'] = 'Слишком маленький вес для выбранного ПВЗ	Слишком маленький вес для выбранного ПВЗ';

        $errors[103]['Error'] = 'ERR_PVZ_WEIGHT_LIMIT';
        $errors[103]['ErrorText'] = 'Ограничение веса в выбранном ПВЗ #var1';

        $errors[104]['Error'] = 'ERR_RECCITYCODE';
        $errors[104]['ErrorText'] = 'Код города получателя отсутствует в базе СДЭК: RecCityCode=#var1	Отсутствие в базе СДЭК передаваемого значение кода города получателя. Необходимо обратиться в поддержку СДЭК.';

        $errors[105]['Error'] = 'ERR_RECCITYPOSTCODE';
        $errors[105]['ErrorText'] = 'Почтовый индекс города получателя отсутствует в базе СДЭК: RecCityPostCode=#var1	Отсутствие в базе СДЭК передаваемого почтового индекса города получателя Необходимо обратиться в поддержку СДЭК.';

        $errors[106]['Error'] = 'ERR_RECCITYPOSTCODE_DUBL';
        $errors[106]['ErrorText'] = 'Невозможно однозначно идентифицировать город получателя по почтовому индексу:  RecCityPostCode=#var1	Индекс относится к нескольким городам. Для однозначного определения населенного пункта необходимо передать код города RecCityCode, либо однозначно определяющий почтовый индекс';

        $errors[107]['Error'] = 'ERR_SCHEDULE_CHANGE';
        $errors[107]['ErrorText'] = 'Заказ не может быть изменен в статусе: #var1	Данные по заказу не могут быть изменены в конечном статусе заказа (Вручен, Не вручен,возврат/Удален).';

        $errors[108]['Error'] = 'ERR_SCHEDULE_DATE';
        $errors[108]['ErrorText'] = 'Дата доставки должна быть больше планируемой даты доставки по прайсу!	Дата доставки в прозвоне не может быть меньше даты создания заказа';

        $errors[109]['Error'] = 'ERR_SCHEDULE_DUBL';
        $errors[109]['ErrorText'] = 'Дублирование номеров заказов в документе: Number=#var1	В документе «Прозвон получателя» данные по одному номеру заказа дублируются: #var1. Необходимо обратиться к программисту ИМ';

        $errors[110]['Error'] = 'ERR_SENDCITYCODE';
        $errors[110]['ErrorText'] = 'Код города отправителя отсутствует в базе СДЭК: SendCityCode=#var1	Отсутствие в базе СДЭК передаваемого значение кода города отправителя. Необходимо обратиться в поддержку СДЭК.';

        $errors[111]['Error'] = 'ERR_SENDCITYPOSTCODE';
        $errors[111]['ErrorText'] = 'Почтовый индекс города отправителя отсутствует в базе СДЭК: SendCityPostCode=#var1	Отсутствие в базе СДЭК передаваемого почтового индекса города отправителя. Необходимо обратиться в поддержку СДЭК.';

        $errors[112]['Error'] = 'ERR_SENDCITYPOSTCODE_DUBL';
        $errors[112]['ErrorText'] = 'Невозможно однозначно идентифицировать город отправителя по почтовому индексу:  SendCityPostCode=#var1	Индекс относится к нескольким городам. Для однозначного определения населенного пункта необходимо передать код города SendCityCode, либо однозначно определяющий почтовый индекс';

        $errors[113]['Error'] = 'ERR_UNKNOWN_DOC_TYPE';
        $errors[113]['ErrorText'] = 'Неизвестный тип документа #var1	В XML передан неизвестный тип документа';

        $errors[114]['Error'] = 'ERR_UNKNOWN_VERSION';
        $errors[114]['ErrorText'] = 'Неизвестная версия #var1 документа #var2	В XML передана неизвестная версия документа';

        $errors[115]['Error'] = 'ERR_WAREKEY_DUBL';
        $errors[115]['ErrorText'] = 'Дублирование идентификатора вложения в упаковке:Package Number=#var1, вложение WareKey=#var2';

        $errors[116]['Error'] = 'ERR_WEIGHT_LIMIT';
        $errors[116]['ErrorText'] = 'Невозможна отправка груза свыше 30 кг. с указанным тарифом.	Невозможна отправка груза свыше 30 кг. с указанным тарифом.';

        $errors[117]['Error'] = 'ERR_XML_EMPTY';
        $errors[117]['ErrorText'] = 'Значение переменной $_POST[xml_request] пустое';


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $errors;

    }
    public function statusList() { //Информация об статусах

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $statusList[0]['State'] = "1";
        $statusList[0]['StateText'] = "Создан";
        $statusList[1]['State'] = "2";
        $statusList[1]['StateText'] = "Удален";
        $statusList[2]['State'] = "3";
        $statusList[2]['StateText'] = "Принят на склад отправителя";
        $statusList[3]['State'] = "6";
        $statusList[3]['StateText'] = "Выдан на отправку в г. отправителе";
        $statusList[4]['State'] = "16";
        $statusList[4]['StateText'] = "Возвращен на склад отправителя";
        $statusList[5]['State'] = "7";
        $statusList[5]['StateText'] = "Сдан перевозчику в г. отправителе";
        $statusList[6]['State'] = "21";
        $statusList[6]['StateText'] = "Отправлен в г. транзит";
        $statusList[7]['State'] = "22";
        $statusList[7]['StateText'] = "Встречен в г. транзите";
        $statusList[8]['State'] = "13";
        $statusList[8]['StateText'] = "Принят на склад транзита";
        $statusList[9]['State'] = "17";
        $statusList[9]['StateText'] = "Возвращен на склад транзита";
        $statusList[10]['State'] = "19";
        $statusList[10]['StateText'] = "Выдан на отправку в г. транзите";
        $statusList[11]['State'] = "20";
        $statusList[11]['StateText'] = "Сдан перевозчику в г. транзите";
        $statusList[12]['State'] = "8";
        $statusList[12]['StateText'] = "Отправлен в г. получатель";
        $statusList[13]['State'] = "9";
        $statusList[13]['StateText'] = "Встречен в г. получателе";
        $statusList[14]['State'] = "10";
        $statusList[14]['StateText'] = "Принят на склад доставки";
        $statusList[15]['State'] = "12";
        $statusList[15]['StateText'] = "Принят на склад до востребования";
        $statusList[16]['State'] = "11";
        $statusList[16]['StateText'] = "Выдан на доставку";
        $statusList[17]['State'] = "18";
        $statusList[17]['StateText'] = "Возвращен на склад доставки";
        $statusList[18]['State'] = "4";
        $statusList[18]['StateText'] = "Вручен";
        $statusList[19]['State'] = "5";
        $statusList[19]['StateText'] = "Не вручен";


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $statusList;

    }

}
