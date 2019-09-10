<?php

namespace pickpoint;

class batches extends \abstracts\batches {

    public function getOrderList($dateFrom, $dateTo) { //Отбор списка заказов за временной интервал

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;
        $query["DateFrom"] = date("d.m.Y", strtotime($dateFrom));
        $query["DateTo"] = date("d.m.Y", strtotime($dateTo));

        $response = $this->authorization->query(json_encode($query), "InvoicesChanging", "POST");
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


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Выборка отправлений за определенный период
        URL: /InvoicesChanging
        Метод: POST
        Описание
        Команда предназначена для получения информации по отправлениям за указанный промежуток времени. В запросе отправляется идентификатор сессии и интервал дат. В ответ возвращается массив номеров отправлений c дополнительной информацией по текущему статусу.
        Структура запроса
        {
            "SessionId": "номер сессии",
            "DateFrom": "<дата "от">",
            "DateTo": "<дата "до">"
        }

        Структура ответа
        {
            "Invoice": [
              "CustomerNumber": " ", -- номер присвойки
              "Encloses": [
                {
                  "Description":              " <описание вложимого>",
                  "Measures": [
                    {
                      "Modified": "< дата изменения>"
                    }
                  ],
                  "Statuses": [ -- статусы вложимого
                    {
                      "Code":                    <код статуса>,
                      "Description":          "<описание>",
                      "Modified":              "05.05.2017 01:03"
                    }
                  ]
                }
              ],
              "Expirations": [ -- дата окончания срока хранения заказа
                {
                  "Date": null,
                  "Modified": "/Date(1493935397847+0300)/"
                }
              ],
              "Forwards": [], -- история перенаправлений
              "Number": "<номер отправления>"

            ]
            "Error": "< ошибка при наличии >"
        }
        */
    }
    public function getLabel($putdata='', $dateFrom='', $dateTo='') { //Печать Наклеек

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;

        if($orders) $query["Invoices"] = $orders;


        // makelabel Формирование этикеток в pdf

        $response = $this->authorization->query(json_encode($query), "makelabel", "POST", false);
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        print $response;

        //return $response;

        /*
        Формирование этикеток в pdf
        URL: /makelabel
        Метод: POST
        Описание
        Команда предназначена для получения этикеток в формате pdf размещаемые на отправлениях. На вход принимается структура, содержащая идентификатор сессии и список номеров отправлений. На выходе массив байт.
        Этикетки можно создавать на отправления в статусах: 101-104.

        Структура запроса
        {
            "SessionId": "<уникальный идентификатор сессии  (GUID 16 байт)>",
            "Invoices": [
                "<номер отправления1>",
                         …
                "<номер отправленияN>"
            ]
        }

        Структура ответа
        В случае ошибки ответ содержит поток с текстом ошибки (начинается с ключевого слова «Error»), в случае успеха ответ содержит поток с pdf файлом в виде массива байт (начинается с «%PDF»).
        */

        //7 makeZLabel Формирование этикеток pdf для принтера Zebra

    }
    public function create($putdata='', $dateFrom='', $dateTo='') { //Создаёт партию

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;

        if($orders) $query["Invoices"] = $orders;


        $response = $this->authorization->query(json_encode($query), "makereestrnumber", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Формирование реестра (по списку отправлений)
        URL: /makereestrnumber
        Метод: POST
        Описание
        Команда предназначена для создания реестра и получения номера данного реестра. На вход принимается структура, содержащая идентификатор сессии и список номеров отправлений. На выход выдается список номеров созданных реестров или сообщение об ошибке. Если все отправления создаются с одним типом передачи отправлений в PickPoint и из одного города, то реестр будет 1.

        Структура запроса
        {
        "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
            "CityName":	"<Название города>",
            "RegionName":	"<Название региона>",
        "Invoices":
         [
            "<номер отправления1>",
                     …
            "<номер отправленияN>"
        ]
        }

        Структура ответа
        {
        "Numbers":
         [
            "<номер реестр1>",
                     …
            "<номер реестрN>"
        ]
        "ErrorMessage":	"<Сообщение ошибки>"
        }

        Внимание! Если вам вернулась ошибка "Не все отправления находятся в статусе «Зарегистрирован» № отправления: 159….» - это означает что вы пытаетесь создать реестр на отправление которое не зарегистрировано или на которое уже был сделан реестр. В таком случае вам необходимо зарегистрировать отправление заново и создать этикетку, а после добавить в реестр.
        */

    }
    public function getInfo($invoiceNumber='', $reestrNumber='') { //Запрашивает данные об партиях

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;

        if($invoiceNumber) $query["InvoiceNumber"] = $invoiceNumber;
        else $query["reestrNumber"] = $reestrNumber;

        $response = $this->authorization->query(json_encode($query), "getreestr", "POST", false);
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*Получение созданного реестра в pdf
        URL: /getreestr
        Метод: POST
        Описание
        Команда предназначена для получения ранее созданного реестра в формате pdf. На вход принимается структура, содержащая идентификатор сессии и номер отправления или номер реестра. Если указанное отправление не содержится ни в одном реестре или нет реестра с указанным номером, вернется соответствующее сообщение.

        Структура запроса
        {
        "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
        "InvoiceNumber":	"<номер отправления>",
        "ReestrNumber":	"<номер реестра>"
        }

        Структура ответа
        В случае ошибки ответ содержит поток с текстом ошибки (начинается с ключевого слова «Error»), в случае успеха ответ содержит поток с pdf файлом в виде массива байт(начинается с «%PDF»).
        */

    }
    public function removeOrder($orderNumber='', $invoiceNumber='') { //Исключение заказа на доставку из всех партий

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        //removeinvoicefromreestr PickPoint Удаление отправления из реестра

        $query["SessionId"] = $this->authorization->sessionId;
        $query["IKN"] = $this->authorization->ikn;

        if($invoiceNumber) $query["InvoiceNumber"] = $invoiceNumber;
        else $query["SenderInvoiceNumber"] = $orderNumber;

        $response = $this->authorization->query(json_encode($query), "removeinvoicefromreestr", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Удаление отправления из реестра
        URL: /removeinvoicefromreestr
        Метод: POST
        Описание
        Команда предназначена для удаления отправлений из реестра передачи клиента. Реестр при этом не должен быть в статусе «Принят». Для успешного удаления отправления его вложимые должны быть в статусах: «Сформирован для передачи Логисту», «Развоз до ПТ самостоятельно», «Сформирован для отправки». В случае, если эти условия не выполнены, то будет выведено соответствующее сообщение.


        Структура запроса
        {
        "SessionId":	"<уникальный идентификатор сессии  (GUID 16 байт), обязательное поле >",
        "IKN": 		"< ИКН – номер договора (10 символов) (10 символов), обязательное поле >",
        "InvoiceNumber":	"<номер отправления>",
        "SenderCode":	"<номер заказа в магазине (50 символов)>"
         }



        Структура ответа
        {
            "ErrorCode":	<Код ошибки, цифра >,
            "ErrorMessage":	"<Описание ошибки, (200 символов), (200 символов)>"
        }

        Поля «InvoiceNumber» и «SenderCode» взаимоисключающие. Если заполнены поля «InvoiceNumber» и «SenderCode», то будет обработан инвойс с указанным InvoiceNumber, если InvoiceNumber не указан, то будет обработан инвойс с указанным SenderCode.
        */

    }

}
