<?php

namespace pickpoint;

class allocates extends \abstracts\allocates {

    public function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo='') { //Вызов курьера для отгрузки

        $batches = new batches($this->authorization);

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;
        $query["IKN"] = $this->authorization->ikn;

        $query["FIO"] = $FIO;
        $query["Phone"] = $Phone;
        $query["Date"] = date("Y.m.d", strtotime($Date));

        if($TimeStart) $query["TimeStart"] = floor((strtotime($TimeStart.':00') - strtotime('00:00:00')) / 60);
        if($TimeEnd) $query["TimeEnd"] = floor((strtotime($TimeEnd.':00') - strtotime('00:00:00')) / 60);
        if($City) $query["City"] = $City;
        if($Address) $query["Address"] = $Address;

        if($Number) $query["Number"] = $Number;
        if($Weight) $query["Weight"] = $Weight;

        $answer = $this->authorization->query(json_encode($query), "courier", "POST");
        if($answer['error']) return ['error' => $answer['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

        /*
        Вызов курьера
        URL: /courier
        Метод: POST
        Описание
        Команда предназначена для создания вызова курьера. На вход принимается структура, содержащая номер сессии и описания адреса забора, времени забора, количества мест и общий вес.
        Внимание! Вызов курьера должен быть в интервале с 9 до 18. Для Московской области вызов курьера на текущий день должен создаваться до 10:30 (диапазон вызова с 9-18 или 10-30 до 18). Для остальных регионов вызов на текущий день должен быть создан до 14:00. Интервал вызова должен быть не менее 4 часов. Структура запроса
        {
            "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
            "IKN":		"< ИКН – номер договора (10 символов)>",
            "SenderCode":	"<Код вызова курьера отправителя, НЕ обязательное поле>",
            "City": 		"<Название города>",
            "City_id": 		<id города>,
            "City_owner_id":	<owner_id город>,
            "Address":	"<Адрес>",
            "FIO": 		"<Контактное лицо>", обязательное поле,
            "Phone": 		"<Контактный телфон>", обязательное поле,
            "Date":		"<Дата сбора>", обязательное поле,//формат гггг.мм.дд
            "TimeStart": 	<Ожидаемое время сбора с>,//количество минут от 00:00
            "TimeEnd":	< Ожидаемое время сбора по>,//количество минут от 00:00
            "Number": 	<Количество мест  – примерное значение >,
            "Weight": 		<Общий вес, кг. – примерное значение>,
        "Comment": 	"<Комментарий>"
        }

        Поля City и City_id/City_owner_id являются взаимоисключающими, при наличии обоих приоритет отдается City_id/City_owner_id
        Значение «TimeStart» для времени 9:00 соответствует 540.
        Значение «TimeEnd» для времени 18:00 соответствует 1080.

        Структура ответа
        {
            "CourierRequestRegistred":	<Признак успешности регистрации вызова курьера >,//<true/false>
            "OrderNumber":			<Номер заказа >,
            "ErrorMessage":			"<Описание ошибки>"
        }
        */

    }
    public function cancel($allocateNumber) { //Отмена отгрузки

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;

        $query["OrderNumber"] = $allocateNumber;

        $answer = $this->authorization->query(json_encode($query), "couriercancel", "POST");
        if($answer['error']) return ['error' => $answer['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

        /*
        Отмена вызова курьера
        URL: /couriercancel
        Метод: POST
        Описание
        Команда предназначена для отмены вызова курьера. На вход принимается структура, содержащая номер сессии и номер вызова курьера.
        Структура запроса
        {
            "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
            "OrderNumber":	"<Номер заказа>"
        }

        Структура ответа
        {
            "OrderNumber":	"<Номер заказа>",
            "Canceled":	"<Результат запроса>"//<true/false>
        }
        */

    }

}
