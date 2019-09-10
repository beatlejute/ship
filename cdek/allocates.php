<?php

namespace cdek;

class allocates extends \abstracts\allocates {

    public function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo='') { //Вызов курьера для отгрузки

        $batches = new batches($this->authorization);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["FIO"] = $FIO;
        $query["Phone"] = $Phone;
        $query["Date"] = date("Y-m-d", strtotime($Date));

        if($TimeStart) $query["TimeStart"] = $TimeStart;
        if($TimeEnd) $query["TimeEnd"] = $TimeEnd;
        if($City) $query["City"] = $City;
        if($Address) $query["Address"] = $Address;

        if($Number) $query["Number"] = $Number;
        if($Weight) $query["Weight"] = $Weight*1000;

        /*
        <?xml version="1.0" encoding="UTF-8" ?>
        <CallCourier ​Date="2011-09-15" Account​="abc123" Secure="abcd1234" CallCount="2" >
            <Call ​Date="2011-09-16" TimeBeg="10:10" TimeEnd="12:10" LunchBeg="12:10" LunchEnd="13:10" SendCityCode="137" SenderName="Трошина Лилия Анатольевна" Weight="3000" Comment="вход с торца" SendPhone="89513752311" >
                <Address ​Street="Восточная" House="6/5" Flat="7" />
            </Call>
            <Call ​Date="2011-09-17" TimeBeg="11:10:18" TimeEnd="12:10:18" LunchBeg="13:10:18" LunchEnd="14:10:18"
            SendCityCode="44" SenderName="Трошина Лилия Анатольевна" Weight="4500" Comment="" SendPhone="89513752314" >
                <Address ​Street="Восточная" House="6/5" Flat="8" />
            </Call>
        </CallCourier>
        */

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<CallCourier Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'" CallCount="1">
							<Call Date="'.$query["Date"].'" TimeBeg="'.$query["TimeStart"].'" TimeEnd="'.$query["TimeEnd"].'" SendCityCode="137" SenderName="'.$query["FIO"].'" Weight="'.$query["Weight"].'" SendPhone="'.$query["Phone"].'">
								<Address Street="'.$query["Address"].'" House="-" Flat="" />
							</Call>
						</CallCourier>';

        $request = array(
            'xml_request' => $xml
        );

        /*
        <?xml version="1.0" encoding="UTF-8"?>
        <response>
            <Order Number="Номер заказа" DispatchNumber ="Номер накладной СДЭК"/>
            <Order Msg="Добавлено заказов CntOrder"/>
        </response>

        <?xml version="1.0" encoding="UTF-8"?>
        <response>
            <Order Number="Номер заказа" ErrorCode="Код ошибки" Msg="Error: описание ошибки"/>
        </response>

        <?xml version="1.0" encoding="UTF-8"?>
        <response>
            <CallCourier Date="2018-03-01"  Msg="Добавлено заявок 1"/>
            <Call Number="9060540"/>
        </response>
        */

        $requestcourier = $this->authorization->query($request, "call_courier.php", "POST", "xml");
        if($requestcourier['error']) return ['error' => $requestcourier['error']];

        /*
        {
          "CourierRequestRegistred": "<Признак успешности регистрации вызова курьера (true/false)>",
          "OrderNumber": "<Номер отгрузки>",
          "ErrorMessage": "<Описание ошибки>"
        }
        */


        if(!$response['CallCourier']['@attributes']['ErrorCode']) {
            $answer["CourierRequestRegistred"] = true;
            $answer["OrderNumber"] = $response['Call']['@attributes']['Number'];
            $answer["ErrorMessage"] = $response['CallCourier']['@attributes']['Msg'];
        } else {
            $answer["CourierRequestRegistred"] = false;
            //$answer["OrderNumber"] = $response['Call']['@attributes']['Number'];
            $answer["ErrorMessage"] = $response['CallCourier']['@attributes']['Msg'];
        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

        /*
        Вызов курьера
        Для использования необходимо отправить POST-запрос на URL: <сервер>/call_courier.php, например,
        https://integration.cdek.ru/call_courier.php, с заполненной переменной $_POST['xml_request'], в которой
        передается содержимое XML-фaйла.
        Описание передаваемых данных:
        № Тэг/Атрибут Описание Тип поля
        Обяз. для
        заполн.
        1. CallCourier Заголовок документа да
        1.2. Date Дата документа (дата вызова) datetime/date да
        1.3. Account Идентификатор ИМ, передаваемый
        СДЭКом.
        string(32) да
        1.4. Secure Ключ (см. п.1.4.) string(32) да
        1.5. CallCount Общее количество заявок для вызова
        курьера в документе. По умолчанию 1.
        integer да
        1.6. Call Ожидание курьера да
        1.6.1. Date Дата ожидания курьера date да
        1.6.2. TimeBeg Время начала ожидания курьера time да
        1.6.3. TimeEnd Время окончания ожидания курьера time да
        1.6.4. LunchBeg Время начала обеда, если входит во
        временной диапазон [TimeBeg; TimeEnd]
        time нет
        1.6.5. LunchEnd Время окончания обеда, если входит во
        временной диапазон [TimeBeg; TimeEnd]
        time нет
        1.6.6. SendCityCode Код города отправителя из базы СДЭК
        (см. файл «City_XXX_YYYYMMDD.xls»)
        integer да
        1.6.7. SendPhone Контактный телефон отправителя phone да
        1.6.8. SenderName Отправитель (ФИО) string(255) да
        1.6.9. Weight Общий вес, в граммах integer да
        1.6.10. Comment Комментарий string(255) нет
        1.6.11 ​Address Адрес отправителя да
        1.6.11.1 Street Улица отправителя. Рекомендуем по возможности не указывать префиксы значений, вроде «ул.»  string(50) да
        1.6.11.2 House Дом, корпус, строение отправителя.
        Рекомендуем по возможности не
        указывать префиксы значений, вроде
        «дом»
        string(30) да
        1.6.11.3 Flat Квартира/Офис отправителя.
        Рекомендуем по возможности не
        указывать префиксы значений, вроде
        «кв.»
        string(10) нет
        Сервер СДЭК вернет результат в виде:
        при удачной обработке данных header("http/1.0 200 Ok");
        и XML в виде:
        <?xml version="1.0" encoding="UTF-8"?>
        <response>
        <Order Number="Номер заказа" DispatchNumber ="Номер накладной СДЭК"/>
        <Order Msg="Добавлено заказов CntOrder"/>
        </response>
        при ошибке: header("http/1.0 200 server error")
        и XML в виде:
        <?xml version="1.0" encoding="UTF-8"?>
        <response>< Order Number="Номер заказа" ErrorCode="Код ошибки» Msg="Error: описание
        ошибки"/></response>
        Пример вызова курьера на ​2011-09-16, и на 2011-09-17:
        <?xml version="1.0" encoding="UTF-8" ?>
        <CallCourier ​Date="2011-09-15"
        Account​="abc123" Secure="abcd1234" CallCount="2" >
        <Call ​Date="2011-09-16" TimeBeg="10:10" TimeEnd="12:10" LunchBeg="12:10" LunchEnd="13:10"
        SendCityCode="137" SenderName="Трошина Лилия Анатольевна" Weight="3000" Comment="вход с торца" SendPhone="89513752311"
        >
        <Address ​Street="Восточная" House="6/5" Flat="7" />
        </Call>
        <Call ​Date="2011-09-17" TimeBeg="11:10:18" TimeEnd="12:10:18" LunchBeg="13:10:18" LunchEnd="14:10:18"
        SendCityCode="44" SenderName="Трошина Лилия Анатольевна" Weight="4500" Comment="" SendPhone="89513752314" >
        <Address ​Street="Восточная" House="6/5" Flat="8" />
        </Call>
        </CallCourier>

        */

    }
    public function cancel($allocateNumber) { //Отмена отгрузки

        return $response['error'] = 'Служба CDEK не поддерживает отмену вызова курьера!';

    }

}
