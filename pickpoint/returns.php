<?php

namespace pickpoint;

class returns extends \abstracts\returns {

    public function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum='') { //Регистрация возврата

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;

        $query["Sendings"][0]["EDTN"] = $orderId;
        $query["Sendings"][0]["IKN"] = $this->authorization->ikn;

        $query["Sendings"][0]["Invoice"]["SenderCode"] = $orderId;
        $query["Sendings"][0]["Invoice"]["InvoiceNumber"] = $invoiceNumber;
        //$query["Sendings"][0]["Invoice"]["AccessCode"] = $AccessCode;
        $query["Sendings"][0]["Invoice"]["Description"] = $description;
        $query["Sendings"][0]["Invoice"]["RecipientName"] = $recipientName;
        $query["Sendings"][0]["Invoice"]["MobilePhone"] = $phone;
        $query["Sendings"][0]["Invoice"]["Email"] = $email;
        $query["Sendings"][0]["Invoice"]["Sum"] = $sum;
        /*$query["Sendings"][0]["Invoice"]["ClientReturnAddress"] = $clientReturnAddress;
        $query["Sendings"][0]["Invoice"]["MobilePhone"] = $mobilePhone;*/


        $response = $this->authorization->query(json_encode($query), "createreturn", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Регистрация возврата
        URL: /createreturn
        Метод: POST
        Описание
        Команда предназначена для создания отправления клиентского возврата на основе обычного отправления. Если вы хотите предоставить покупателю возможность вернуть вам заказ через постамата, то необходимо зарегистрировать это отправление в системе PickPoint и сообщить покупателю код для осуществления данной услуги. Покупатель сможет ввести код в меню постамата и заложить отправление для доставки его в магазин.
         Если InvoiceNumber указан, то возврат создается на указное отправление. Иначе создается почтовый возврат.

        Структура запроса
        {
            "SessionId":"<уникальный идентификатор сессии  (GUID 16 байт)>",
            "Sendings":
            [
                {
                    "EDTN":		"<Идентификатор запроса, используемый для ответа. Указывайте уникальное число>",
                    "IKN": 		"<ИКН – номер договора>",
                    "Invoice":
                    {
                        "SenderCode":	"<Номер заказа магазина>",
                        "AccessCode":	"<Код закладки>",
                        "Description":	"<Описание отправления, обязательное поле>",
                        "RecipientName":	"<Имя получателя>",
                        "InvoiceNumber": 	"<Номер КО в систем PickPoint (20 символов)>",
                        "MobilePhone": 	"<Номер телефона получателя, обязательное поле >",
                        "Email": 		"<Адрес электронной почты получателя>",
                        "Sum": 		<Сумма>,
                        "ClientReturnAddress":	"<Адрес клиентского возврата, если не указано, берется из котракта>"
                        {
                            "CityName":	"<Название города, обязательное поле >",
                            "RegionName":	"<Название региона>",
                            "Address":	"<Текстовое описание адреса, обязательное поле >",
                            "FIO":		"<ФИО контактного лица>",
                            "PostCode":	"<Почтовый индекс>",
                            "Organisation":	"<Наименование организации>",
                            "PhoneNumber":	"<Контактный телефон, обязательное поле>",
                            "Comment":	"<Комментарий>"
                        },
                        "Places": [
                            {
                                "BarCode": 	"<Штрих код от PickPoint. Отправляйте поле пустым, в ответ будет ШК >",
                                "GCBarCode":	"<Клиентский штрих-код. Поле не обязательное. Можно не отправлять >",
                                "Width": 		<Ширина>,
                                "Height": 		<Высота>,
                                "Depth": 		<Глубина>,
                                "SubEncloses":	<Субвложимые>
                                    [
                                        {
                                            "Line":		"<Номер>",
                                            "ProductCode":	"<Код продукта>",
                                            "GoodsCode":	"<Код товара>",
                                            "name":		"<Наименование>",
                                            "Price":		<Стоимость>
                                        }
                                    ]
                            }
                        ]
                    }
                }
            ]
        }

        Описание полей:
        SenderCode	Номер заказа магазина. строка 50 символов
        BarCode	Значение Штрих Кода. Если вам не выделили диапазон ШК, отправляйте поле пустым
        Description	Описание типа вложимого, Пример: «Одежда и Обувь». строка 200 символов, обязательное поле
        RecipientName	Имя получателя, строка 60 символов, обязательное поле
        MobilePhone	Номер Мобильного телефона получателя для SMS, формат номера: 7/8хххХХХххХХ  обязательное поле
        Email	Email, строка 256 символов
        Width	Ширина, в см, обязательное поле Если не знаете точных габаритов указывайте примерные значения или =0. (при типе сдаче в окне приема ПТ (валом или по ячейкам).
        Height	Высота, в см, обязательное поле Если не знаете точных габаритов указывайте примерные значения или =0
        Depth	Глубина в см, обязательное поле Если не знаете точных габаритов указывайте примерные значения или =0

        Структура ответа
        {
            "CreatedSendings":
            [
                {
                    "EDTN":		"< Значение идентификатора запроса >",
                    "ReturnCode":	"<Код для возврата отправления на постамате>",
                    "InvoiceNumber":	"<Номер отправления присвоенный PickPoint>",
                    "Places":
                    [
                        {
                        "GCBarCode":	"<Клиентский номер ШК>",
        "Barcode":	"< Штрих код от PickPoint >"
        }
                    ]
        }
        ]

            "RejectedSendings":
            [
                {
                    "EDTN":		"< Значение идентификатора запроса >",
                    "ErrorCode":	<Код ошибки>,
                    "ErrorMessage":	"<Описание ошибки>"
        }
        ]
        }
        */

    }
    public function getReturnsList($dateFrom, $dateTo) { //Получение списка возвратных отправлений

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;
        $query["DateFrom"] = date("d.m.Y", strtotime($dateFrom));
        $query["DateTo"] = date("d.m.Y", strtotime($dateTo));

        //print_r($query);

        $response = $this->authorization->query(json_encode($query), "getreturninvoiceslist", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Получение списка возвратных отправлений
        URL: /getreturninvoiceslist
        Метод: POST
        Описание
        Команда предназначена для получения списка возвратных отправлений, которые покупатель возвращает или вернул в магазин через постамат PickPoint. В запросе отправляется идентификатор сессии и интервал дат, за которые необходимо получить список. В ответ возвращается список отправлений с параметрами, либо ошибка.
        Структура запроса
        {
            "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
            "DateFrom":	"<дата, с которой необходимо получить список >",
            "DatetTo":	"<дата, до которой необходимо получить список>"
        }

        Поля InvoiceNumber и SenderInvoiceNumber являются взаимоисключающими
        Структура ответа
        {
            SendingsInfo
            [
                {

                    " InvoiceNumber":		"<номер отправления PickPoint>",
                    " SenderInvoiceNumber":	"<номер отправления, введенный получателем>",
                    " Barcode":		"<штрих-код PickPoint>",
                    " ConsultantNumber":	"<номер консультанта, если был указан>",
                    " DateOfCreate":		"<дата создания отправления>",
                    " PhoneNumber":		"<номер телефона, введенный покупателем (отправителем)>",
                    " ReturnReason":		"<указанная причина возврата>"
                }
            ]
            "Error":	"<описание ошибки>"
        }
        */

    }
    public function getInfo($orders='', $dateFrom='', $dateTo='') { //Получение информации по возвратным отправлениям

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["SessionId"] = $this->authorization->sessionId;

        if($orders) $query["Invoices"] = $orders;


        $response = $this->authorization->query(json_encode($query), "getreturn", "POST");
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Получение информации по возвратной накладной
        URL:  /getreturn
        Метод: POST
        Описание
        Команда предназначена для получения номера возвратной накладной и, если есть, номера акта возврата.

        Структура запроса
        {
        "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
        "Invoices":
         [
                "<номер отправления Pickpoint или номер отправления магазина 1>",
                      …
                "<номер отправления Pickpoint или номер отправления магазина N>"
        ]
        }

        Структура ответа
        {
        "SessionId":	"<уникальный идентификатор сессии (GUID 16 байт)>",
        "ReturnInvoices":
         [
                {
                    "InvoiceNumber":		"<номер отправления Pickpoint>",
                    "ReturnInvoiceNumber"	:"<номер отправления магазина (присвойка)>",
            "ReturnDocumentNumber":	"<номер акта возврата товара>",
        "ReturnBarcodes":
        [
            "<штрих-код отправления>"
            …
            "<штрих-код отправления>"
        ]
        }
        ],
            "ErrorCode":	<Код ошибки: 0 – нет ошибки, -1 - ошибка>,
        "ErrorMessage":	"<Описание ошибки>"
        }
        */

    }

}

?>