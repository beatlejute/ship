<?php

namespace cdek;

class batches extends \abstracts\batches {

    public function getOrderList($dateFrom, $dateTo) { //Отбор списка заказов за временной интервал

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["DateFrom"] = date("Y-m-d", strtotime($dateFrom));
        $query["DateTo"] = date("Y-m-d", strtotime($dateTo." 23:59"));

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
						<StatusReport Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'">
							<ChangePeriod DateFirst="'.$query["DateFrom"].'" DateLast="'.$query["DateTo"].'"  />
						</StatusReport>';

        $request = array(
            'xml_request' => $xml
        );

        $requestinfo = $this->authorization->query($request, "status_report_h.php", "POST", "xml");
        if(isset($requestinfo['error'])) return ['error' => $requestinfo['error']];

        if(!$requestinfo) {

            $answer['error']['code'] = "_1";
            $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

            return $answer;

        }
        if(!is_array($requestinfo)) {

            $answer['error']['code'] = "_2";
            $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
            $answer['error']['info'][] = print_r($requestinfo, true);

            return $answer;

        }

        foreach($requestinfo['Order'] as $orderId => $order) {

            $response['Invoices'][$orderId]['CustomerNumber'] = $order['@attributes']['Number'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Code'] = $order['Status']['@attributes']['Code'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Description'] = $order['Status']['@attributes']['Description'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Modified'] =  date("d.m.Y H:i:s", strtotime($order['Status']['@attributes']['Date']));
            $response['Invoices'][$orderId]['Number'] = $order['@attributes']['DispatchNumber'];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        Отчет «Статусы заказов»
        Для использования необходимо отправить POST запрос на URL: <сервер>/status_report_h.php, например,    https://integration.cdek.ru/status_report_h.php с заполненной переменной $_POST['xml_request'], в которой передается содержимое XML файла (описание см. ниже). В результате также возвращается XML (описание см. ниже). Либо отправляет GET запрос с заполненными переменными $_GET['account'], $_GET['secure'], $_GET['datefirst'], $_GET['datelast'], $_GET['showhistory']. Пример запроса:
        https://integration.cdek.ru/status_report_h.php?account=abcd1234567890efgh&secure=abcd1234567890efghxwz&datefirst=2010-10-1T00:00:00&showhistory=1, где datefirst – дата начала запрашиваемого периода изменения статусов, datelast – дата окончания. datelast – необязательный параметр, в случае его отсутствия используется текущая дата время осуществления запроса. Для формирования secure используется параметр datefirst.
        Запрос статусов можно делать так часто, как этого требуется ИМ.

        Рекомендуем делать запросы и актуализировать информацию в ИС ИМ не реже 1 раза в сутки.
        Описание передаваемых данных  1:
        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	StatusReport	Заголовок документа		да
        1.1.	 Date	Дата запроса	datetime/date	да
        1.2.	 Account	Идентификатор ИМ, передаваемый СДЭКом.	string(255)	да
        1.3.	 Secure	Ключ (см. п. 1.4.)
        string(255)	да
        1.4.	 ShowHistory	Атрибут, указывающий на необходимость загружать историю заказов (1-да, 0-нет)	boolean	нет
        1.5. 	ShowReturnOrder 	Атрибут, указывающий на необходимость загружать список возвратных заказов (1-да, 0-нет)	boolean	нет
        1.6.	ShowReturnOrderHistory 	Атрибут, указывающий на необходимость загружать историю возвратных заказов (1-да, 0-нет)	boolean	нет
        1.7.	 ChangePeriod  2	Период, за который произошло изменение  статуса заказа.		нет
        1.7.1.	  DateFirst	Дата начала запрашиваемого периода	datetime/date	да
        1.7.2.	  DateLast	Дата окончания запрашиваемого периода	datetime/date	нет
        1.8.	 Order  2	Отправление (заказ)		нет
        1.8.1.	  DispatchNumber  3	Номер отправления СДЭК (присваивается при импорте заказов). Идентификатор заказа в ИС СДЭК.	integer	да
        1.8.2.	  Number  3	Номер отправления клиента. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        1.8.3.	  Date  3	Дата акта приема-передачи, в котором был передан заказ	date	да

        1  При использовании POST запроса.
        2  Запрос должен содержать хотя бы один из тэгов  ChangePeriod или Order.
        Если указан тэг ChangePeriod и список заказов в тэге Order, то результат будет содержать информацию по заказам, которые изменили статус в указанный период.
        Если указан тэг ChangePeriod и отсутствует список Order, то результат будет содержать информацию по всем заказам, которые изменили статус в указанный период.
        Если тэг ChangePeriod не передан, присутствует только Order, передается информация по всему списку запрашиваемых заказов.
        3  Идентификация заказа осуществляется либо по «DispatchNumber», либо по двум параметрам «Number», Date. Если в запросе есть значение атрибута «DispatchNumber», то атрибуты «Number», «Date» игнорируются.
        Описание получаемых данных:
        №	Тэг/Атрибут	Описание	Тип поля	Обязат. для заполн.
        1.	StatusReport	Заголовок документа		да
        1.1.	 DateFirst	Дата и время начала периода изменений по статусам заказа	datetime/date	да
        1.2.	 DateLast	Дата и время окончания периода изменений по статусам заказа	datetime/date	да
        2.	 Order	Отправление (Заказ)		да
        2.1.	  ActNumber	Номер акта приема-передачи	string(30)	да
        2.2.	  Number	Номер отправления клиента. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        2.3.	  DispatchNumber	Номер отправления СДЭК (присваивается при импорте заказов). Идентификатор заказа в ИС СДЭК.	integer	да
        2.4.	  DeliveryDate	Дата доставки	datetime	нет
        2.5.	  RecipientName	Получатель при доставке	string(50)	нет
        2.6.	  ReturnDispatchNumber	Номер возвратного отправления (номер накладной, в которой возвращается товар ИМ в случае статусов «Не вручен», «Вручен» - «Частичная доставка»)	integer	нет
        2.7	  Status	Текущий статус заказа		да
        2.7.1	   Date	Дата статуса	datetime	да
        2.7.2	   Code	Код статуса
        (см. Приложение, таблица 2)
        integer	да
        2.7.3	   Description	Название статуса	string(100)	да
        2.7.4	   CityCode	Город изменения статуса, код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        2.7.5	   CityName	Наименование города изменения статуса	string(100)	да
        2.7.6	   State  1	История изменений статусов		да
        2.7.6.1	    Date	Дата статуса	datetime	да
        2.7.6.2	    Code	Код статуса
        (см. Приложение, таблица 2)
        integer	да
        2.7.6.3	    Description	Название статуса	string(100)	да
        2.7.6.4	    CityCode	Город изменения статуса, код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        2.7.6.5	     CityName	Наименование города изменения статуса	string(100)	да
        2.8	  Reason	Текущий дополнительный статус		да
        2.8.1	   Date	Дата дополнительного статуса	datetime	да
        2.8.2	   Code	Код дополнительного статуса
        (см. Приложение, таблица 3)
        integer	нет
        2.8.3	   Description	Название дополнительного статуса	string(100)	нет
        2.9	  DelayReason	Текущая причина задержки		да
        2.9.1	   Date	Дата причины задержки	datetime	да
        2.9.2	   Code	Код причины задержки
        (см. Приложение, таблица 4)
        integer	нет
        2.9.3	   Description	Причина задержки	string(50)	нет
        2.9.4	   State  1	История причин задержек		да
        2.9.4.1	    Date	Дата причины задержки	datetime	да
        2.9.4.2	    Code	Код причины задержки
        (см. Приложение, таблица 4)	integer	нет
        2.9.4.3	    Description	Причина задержки	string(50)	нет
        2.10	  Package  1	Упаковка		нет
        2.10.1	   Number	Номер упаковки. Идентификатор заказа в ИС клиента СДЭК.	string(20)	да
        2.10.2	   Item  2	Вложение		да
        2.10.2.1	    WareKey	Идентификатор/артикул товара/вложения (Уникален в пределах упаковки Package).	string(20)	да
        2.10.2.2	    DelivAmount	Количество доставленных единиц вложения. По умолчанию равно количеству переданного на доставку товара.	integer	да
        2.11	  Attempt  3	Время доставки из расписания на доставку		нет
        2.11.1	   ID	Идентификационный номер расписания по базе ИМ	integer	да
        2.11.2	   ScheduleCode	Код причины задержки
        (см. Приложение, таблица 4)
        integer	да
        2.11.3	   ScheduleDescription	Причина задержки	string(50)	да
        2.12	  Call	История прозвонов получателя		нет
        2.12.1	   CallGood	История удачных прозвонов		нет
        2.12.1.1	    Good	Удачный прозвон		да
        2.12.1.1.1	     Date	Дата неудачного прозвона	date	да
        2.12.1.1.2	     DateDeliv	Дата, на которую договорились о доставке/самозаборе	date	да
        2.12.2	   CallFail	История неудачных прозвонов		нет
        2.12.2.1	    Fail	Неудачный прозвон		да
        2.12.2.1.1	     Date	Дата прозвона	date	да
        2.12.2.1.2	     ReasonCode	Код причины неудачного прозвона (см. Приложение, таблица 6)
        integer	да
        2.12.2.1.3	     ReasonDescription	Причина неудачного прозвона	string(255)	да
        2.12.3	   CallDelay	История переносов прозвона		нет
        2.12.3.1	    Delay	Перенос прозвона		да
        2.12.3.1.1	     Date	Дата прозвона	date	да
        2.12.3.1.2	     DateNext	Дата, на которую перенесен прозвон	date	да
        3.	 ReturnOrder	Возвратное отправление 		нет
        3.1.	  ReturnOrderNumber	Номер акта приема-передачи	string(30)	да
        3.2.	  Number	Номер отправления клиента. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        3.3.	  DispatchNumber	Номер возвратной накладной СДЭК (присваивается при согласовании возвратной ведомости). Идентификатор заказа в ИС СДЭК.	integer	да
        3.4.	  DeliveryDate	Дата доставки	datetime	нет
        3.5.	  RecipientName	Получатель при доставке	string(50)	нет
        3.6	  Status	Текущий статус заказа		да
        3.6.1	   Date	Дата статуса	datetime	да
        3.6.2	   Code	Код статуса
        (см. Приложение, таблица 2)
        integer	да
        3.6.3	   Description	Название статуса	string(100)	да
        3.6.4	   CityCode	Город изменения статуса, код города по базе СДЭК (см. файл «City_XXX_YYYYMMDD.xls»)	integer	да
        3.6.5	   CityName	Наименование города изменения статуса	string(100)	да
        3.6.6	   State  1	История изменений статусов		да
        3.6.6.1	    Date	Дата статуса	datetime	да
        3.6.6.2	    Code	Код статуса
        (см. Приложение, таблица 2)
        integer	да
        3.6.6.3	    Description	Название статуса	string(100)	да
        3.6.6.4	    CityCode	Город изменения статуса	integer	да
        3.6.6.5	   CityName	Наименование города изменения статуса	string(100)	да
        3.7	  Reason	Текущий дополнительный статус		да
        3.7.1	   Date	Дата дополнительного статуса	datetime	да
        3.7.2	   Code	Код дополнительного статуса
        (см. Приложение, таблица 3)
        integer	нет
        3.7.3	   Description	Название дополнительного статуса	string(100)	нет
        3.8	  DelayReason	Текущая причина задержки		да
        3.8.1	   Date	Дата причины задержки	datetime	да
        3.8.2	   Code	Код причины задержки
        (см. Приложение, таблица 4)
        integer	нет
        3.8.3	   Description	Причина задержки	string(50)	нет

        1  Тэг «State» присутствует только при значении параметра «ShowHistory. = 1 (см. описание передаваемых данных).
        2 Тэги «Package», «Item» присутствуют только при полном вручении заказа (в конечном статусе «Вручен») и  при частичной доставке в конечном статусе «Не вручен» и  дополнительном статусе «Частичная доставка».
        3  Тэг Attempt присутсвует только в случае, если по условиям договора, ИМ самостоятельно предоставляет расписание доставки для СДЭК. Тэг содержит данные по неудачным попыткам доставки в разрезе предоставленного ИМ расписания доставки.
         1.Документ содержит передаваемые данные для отчета «Статусы заказов»: все изменения статуса заказов с 2013-07-16 по 2013-07-17.
        <?xml version="1.0" encoding="UTF-8" ?>
        <StatusReport Date="2013-07-17" Account="123"
          Secure="123"  ShowHistory="1">
        <ChangePeriod DateFirst="2013-07-16" DateLast="2013-07-17"/>
        </StatusReport>
        2.Документ содержит передаваемые данные для отчета «Статусы заказов»: все изменения статуса указанных заказов с 2013-07-16 по 2013-07-17.
        <?xml version="1.0" encoding="UTF-8" ?>
        <StatusReport Date="2013-07-17" Account="123" Secure="123" ShowHistory="1">
        <ChangePeriod DateFirst="2013-07-16" DateLast="2013-07-17"/>
        <Order Number="6346860" Date="2013-07-04" />
        <Order Number ="6346869" Date="2013-07-16" />
        </StatusReport>
        Отчет содержит данные о доставке по двум заказам. Заказ с номером отправления 6346860 вручен с частичной доставкой(вложения с идентификаторами 25000050368, 25000348563 вручены, вложения 25000373314, 25000390270 будут возвращены ИМ), заказ 6346869 — в статусе «возвращен на склад доставки» после неудачной попытки доставки по причине «Контактное лицо отсутствует».
        Параметр showhistory=1: в отчете отображается история заказа с даты DateFirst.
        Возвращаемый результат:
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
        3. Документ содержит передаваемые данные для отчета «Статусы заказов»: все изменения статуса заказа с номером накладной 1014263974.

        <?xml version="1.0" encoding="UTF-8"?> <StatusReport Date="2015-04-10T00:00:00" Account="123"  Secure="123" ShowHistory="1" ShowReturnOrder="1" ShowReturnOrderHistory="1">
        <Order DispatchNumber="1014263974">
        </Order>  <
        /StatusReport>


        Или для просмотра всех изменений статуса и возвратные статусы заказа с номером ИМ 51844.

        <?xml version="1.0" encoding="UTF-8"?>
        <StatusReport Date="2015-04-10T00:00:00" Account="123"
          Secure="123" ShowHistory="1" ShowReturnOrder="1" ShowReturnOrderHistory="1">
           <Order Number="51844" Date="2015-03-24"/>
          </StatusReport>


        Возвращаемый результат:

        <?xml version="1.0" encoding="UTF-8"?>
        <StatusReport DateFirst="2000-12-31T18:00:00+00:00" DateLast="2015-10-15T09:59:35+00:00">
           <Order ActNumber="51844" Number="51844" DispatchNumber="1014263974"   DeliveryDate="" RecipientName="" ReturnDispatchNumber="1014526920">
               <Status Date="2015-04-13T03:14:41+00:00" Code="5" Description="Не вручен" CityCode="723" CityName="Северск">
           <State Date="2015-03-24T09:28:14+00:00" Code="1" Description="Создан" CityCode="44" CityName="Москва"/>
           <State Date="2015-03-24T16:35:04+00:00" Code="3" Description="Принят на склад отправителя" CityCode="44" CityName="Москва"/>
           <State Date="2015-03-24T18:54:18+00:00" Code="6" Description="Выдан на отправку в г.-отправителе" CityCode="44" CityName="Москва"/>
           <State Date="2015-03-24T19:56:22+00:00" Code="7" Description="Сдан перевозчику в г.-отправителе" CityCode="44" CityName="Москва"/>
           <State Date="2015-03-26T04:45:54+00:00" Code="13" Description="Принят на склад транзита" CityCode="269" CityName="Томск"/>
           <State Date="2015-03-26T05:15:08+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="269" CityName="Томск"/>
           <State Date="2015-03-26T06:06:55+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="269" CityName="Томск"/>
           <State Date="2015-03-26T06:07:06+00:00" Code="8" Description="Отправлен в г.-получатель" CityCode="269" CityName="Томск"/>
           <State Date="2015-03-26T06:31:01+00:00" Code="12" Description="Принят на склад до востребования" CityCode="723" CityName="Северск"/>
           <State Date="2015-04-13T03:14:41+00:00" Code="5" Description="Не вручен" CityCode="723" CityName="Северск"/>
               </Status>
               <Reason Code="23" Description="Возврат, истек срок хранения" Date="2015-04-13 09:14:41"/>
               <DelayReason Code="" Description="" Date=""/>
               <Call>
           <CallGood>
           <Good Date="2015-03-31T07:12:49+00:00" DateDeliv="2015-04-09"/>
           </CallGood>
               </Call>
               <ReturnOrder ActNumber="" Number="" DispatchNumber="1014526920"   DeliveryDate="2015-04-17T15:35:52+00:00" RecipientName="Бамболо, продавец в магазине" >
           <Status Date="2015-04-17T12:31:45+00:00" Code="4" Description="Вручен" CityCode="44" CityName="Москва">
           <State Date="2015-04-13T08:50:54+00:00" Code="1" Description="Создан" CityCode="723" CityName="Северск"/>
           <State Date="2015-04-13T08:51:05+00:00" Code="3" Description="Принят на склад отправителя" CityCode="723" CityName="Северск"/>
           <State Date="2015-04-13T09:53:24+00:00" Code="6" Description="Выдан на отправку в г.-отправителе" CityCode="723" CityName="Северск"/>
           <State Date="2015-04-13T09:55:14+00:00" Code="7" Description="Сдан перевозчику в г.-отправителе" CityCode="723" CityName="Северск"/>
           <State Date="2015-04-13T09:55:37+00:00" Code="21" Description="Отправлен в г.-транзит" CityCode="723" CityName="Северск"/>
           <State Date="2015-04-13T10:35:24+00:00" Code="13" Description="Принят на склад транзита" CityCode="269" CityName="Томск"/>
           <State Date="2015-04-13T12:33:59+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="269" CityName="Томск"/>
           <State Date="2015-04-13T12:52:03+00:00" Code="21" Description="Отправлен в г.-транзит" CityCode="269" CityName="Томск"/>
           <State Date="2015-04-13T18:21:06+00:00" Code="13" Description="Принят на склад транзита" CityCode="270" CityName="Новосибирск"/>
           <State Date="2015-04-14T07:08:35+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="270" CityName="Новосибирск"/>
           <State Date="2015-04-14T12:36:37+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="270" CityName="Новосибирск"/>
           <State Date="2015-04-14T12:36:43+00:00" Code="21" Description="Отправлен в г.-транзит" CityCode="270" CityName="Новосибирск"/>
              <State Date="2015-04-15T01:58:04+00:00" Code="13" Description="Принят на склад транзита" CityCode="268" CityName="Омск"/>
             <State Date="2015-04-15T01:58:04+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="268" CityName="Омск"/>
           <State Date="2015-04-15T02:00:37+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="268" CityName="Омск"/>
           <State Date="2015-04-15T02:00:43+00:00" Code="21" Description="Отправлен в г.-транзит" CityCode="268" CityName="Омск"/>
           <State Date="2015-04-15T11:39:23+00:00" Code="13" Description="Принят на склад транзита" CityCode="252" CityName="Тюмень"/>
           <State Date="2015-04-15T11:39:23+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="252" CityName="Тюмень"/>
           <State Date="2015-04-15T12:48:56+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="252" CityName="Тюмень"/>
           <State Date="2015-04-15T18:32:33+00:00" Code="21" Description="Отправлен в г.-транзит" CityCode="252" CityName="Тюмень"/>
           <State Date="2015-04-15T20:19:02+00:00" Code="13" Description="Принят на склад транзита" CityCode="250" CityName="Екатеринбург"/>
           <State Date="2015-04-15T20:19:02+00:00" Code="19" Description="Выдан на отправку в г.-транзите" CityCode="250" CityName="Екатеринбург"/>
           <State Date="2015-04-15T20:49:31+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="250" CityName="Екатеринбург"/>
           <State Date="2015-04-15T20:49:44+00:00" Code="8" Description="Отправлен в г.-получатель" CityCode="250" CityName="Екатеринбург"/>
           <State Date="2015-04-15T20:51:33+00:00" Code="20" Description="Сдан перевозчику в г.-транзите" CityCode="250" CityName="Екатеринбург"/>
           <State Date="2015-04-15T20:52:16+00:00" Code="8" Description="Отправлен в г.-получатель" CityCode="250" CityName="Екатеринбург"/>
           <State Date="2015-04-17T07:49:17+00:00" Code="10" Description="Принят на склад доставки" CityCode="44" CityName="Москва"/>
           <State Date="2015-04-17T09:28:50+00:00" Code="11" Description="Выдан на доставку" CityCode="44" CityName="Москва"/>
           <State Date="2015-04-17T12:31:45+00:00" Code="4" Description="Вручен" CityCode="44" CityName="Москва"/>
           </Status>
           <Reason Code="" Description="" Date=""/>
           <DelayReason Code="" Description="" Date=""/>
               </ReturnOrder>
           </Order>
        </StatusReport>
        */
    }
    public function getLabel($putdata='', $dateFrom='', $dateTo='') { //Печать Наклеек

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        /*
        <?xml version="1.0" encoding="UTF-8" ?>
        <OrdersPrint Date="2011-09-15" Account="123" Secure="123"  OrderCount="3" CopyCount="4">
            <Order Number="634686069092845559" Date="2012-03-29" />
        </OrdersPrint>
        */

        $xml .= '<?xml version="1.0" encoding="UTF-8" ?>
					<OrdersPrint Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'" OrderCount="'.sizeof($orders).'" CopyCount="1">';

        foreach($orders as $orderId => $order) {

            $xml .= '<Order DispatchNumber="'.$order.'" />';

        }

        $xml .= '</OrdersPrint>';

        $request = array(
            'xml_request' => $xml
        );

        $response = $this->authorization->query($request, "orders_print.php", "POST", false);
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        print $response;

        //return $response;

        /*
        Печатная форма квитанции к заказу
        Для использования необходимо отправить POST запрос на URL: <сервер>/orders_print.php, например,  https://integration.cdek.ru/orders_print.php. с заполненной переменной $_POST['xml_request'], в которой передается содержимое XML файла.

        С целью предотвращения перегрузки платформы больше 100 номеров заказов за один раз передавать нельзя.

        Описание передаваемых данных:
        №	Тэг/Атрибут	Описание	Тип поля	Обяз. для заполн.
        1.	OrdersPrint	Заголовок документа		да
        1.1.	 Date	Дата документа (дата заказа)	datetime/date	да
        1.2.	 Account	Идентификатор ИМ, передаваемый СДЭКом.	string(255)	да
        1.3.	 Secure	Ключ (см. п.1.4.)
        string(255)	да
        1.4.	 OrderCount	Общее количество передаваемых в документе заказов	integer	да
        1.5.	 CopyCount	Число копий одной квитанции на листе. Рекомендовано указывать не менее 2, одна приклеивается на груз, вторая остается у отправителя.	integer	нет
        2.	 Order	Отправление (заказ)		да
        2.1.	  DispatchNumber  1	Номер отправления СДЭК (присваивается при импорте заказов). Идентификатор заказа в ИС клиента СДЭК.	integer	да
        2.2.	  Number  1	Номер отправления клиента. Идентификатор заказа в ИС клиента СДЭК.	string(30)	да
        2.3.	  Date  1	Дата акта приема-передачи, в котором был передан заказ	date	да

        1  Идентификация заказа осуществляется либо по «DispatchNumber», либо по двум параметрам «Number», «Date». Если в запросе есть значение атрибута «DispatchNumber», то атрибуты «Number», «Date» игнорируются.

         При отправке такого запроса на выходе получим 1 pd-файл, который содержит пакет из 3-х заказов каждый заказ будет выведен в количестве 2-х штук: 2 заказа (первый и третий) указаны номером отправления клиента СДЭК, второй номером накладной СДЭК.
        <?xml version="1.0" encoding="UTF-8" ?>
        <OrdersPrint Date="2011-09-15"
        Account="123" Secure="123"  OrderCount="3" CopyCount="2">
        <Order Number="634686069092845559" Date="2012-03-29" />
        <Order DispatchNumber="2894484" />
        <Order Number ="634686069092845560" Date="2012-03-29" />
        </OrdersPrint>

        При отправке такого запроса на выходе получим 1 pdf-файл, который содержит пакет из одного заказа в количестве 4-х штук, заказ указан номером отправления клиента СДЭК.
        <?xml version="1.0" encoding="UTF-8" ?>
        <OrdersPrint Date="2011-09-15"
        Account="123" Secure="123"  OrderCount="3" CopyCount="4">
        <Order Number="634686069092845559" Date="2012-03-29" />
        </OrdersPrint>

          Пример pdf – файла

        Описание атрибутов pdf – файла:
        Номер на рисунке	Параметр при формировании
        заказа	Описание
        1	Определяется в соответствии с переданными параметрами Order.RecCityCode и/или
        Order.RecCityPostCode	Город получателя
        2	DisptchNumer	Номер накладной СДЭК
        3	Order.Number	Номер заказа
        4	DeliveryRequest.Date	Дата акта (заказа)
        5	Определяется в соответствии с указанным параметром DeliveryRequest.Account 	Название юр. лица  ИМ зарегистрированного в СДЭК
        6	Order.SellerName	Название продавца, рекомендуем указывать в данном параметре значение, по которому получатель однозначно поймет от кого ему придет посылка, например название сайта на котором производится заказ товара или название ИМ
        7	Количество секций Order.Package	Количество грузомест в посылке
        8	Сумма всех Package.Weight	Суммарный вес всех грузомест
        9	Package.BarCode	Штрих - коды всех грузомест перечисленные через запятую
        10	Order.TariffTypeCode	Название тарифа (услуги доставки)
        11	Определяется в соответствии с переданным Order.TariffTypeCode	Режим доставки груза: Д-до двери (доставка курьером), С-до склада (самозабор)
        12	-	Параметр договора,  кто прозванивает получателя, чтобы согласовать дату и время доставки: перечеркнутый телефон – прозванивает сам ИМ, не перечеркнутый – СДЭК
        13	Order.Adrress	Адрес доставки груза: при режиме доставки до двери – адрес получателя,  куда курьер СДЭК повезет груз, при режиме доставки до склада – адрес ПВЗ, для осуществления самозабора
        14	Order.Phone	Контактный телефон получателя для осуществления уведомлений
        15	Order.RecMan	ФИО получателя
        16	Order.Comment	Комментарий (примечание) к заказу
        17	Item.WareKey и Order.Comment	Артикул и наименование товара
        18	AddService.ServiceCode=2	Дополнительная услуга «Страхование», назначается  автоматически, в данной печатной форме стоимость всегда равна 0, но при этом в счете для ИМ рассчитывается согласно алгоритма расчета данной доп. услуги
        19	AddService.ServiceCode	Дополнительная услуга « Примерка на дому», может быть указана дополнительно при формировании заказа, не является обязательной, в данной печатной форме стоимость всегда равна 0
        20	Item.Cost	Объявленная стоимость (цена) в указанной валюте за единицу товара
        21	Item.Amount	Количество одной товарной позиции в заказе
        22	Item.Cost* Item.Amount	Итоговая стоимость за все единицы товара, т.е. объявленная стоимость (цена) умноженная на количество
        23	Item.Payment *Item.Amount	Сумма к оплате в валюте, при полной предоплате равна 0, т.е. сколько взять с получателя при вручении данного товара
        24	Сумма( Item.Payment *Item.Amount)	Итого, сколько взять с получателя за все товарные позиции, при полной предоплате равна 0
        25	Order.DeliveryRecipientCost	Стоимость доставки, рассчитанная заранее ИМ с помощью API калькулятора СДЭК или иным способом и переданная при формировании заказа
        26	Сумма( Item.Payment *Item.Amount) +
        Order.DeliveryRecipientCost	Итого к оплате при вручении с учетом доставки 24+25

        */

    }
    public function create($orders='', $dateFrom='', $dateTo='') { //Создаёт партию

        return $response['error'] = 'Служба CDEK не поддерживает партии!';

    }
    public function getInfo($invoiceNumber='', $reestrNumber='') { //Запрашивает данные об партиях

        return $response['error'] = 'Служба CDEK не поддерживает партии!';

    }
    public function removeOrder($orderNumber='', $invoiceNumber='') { //Исключение заказа на доставку из всех партий

        return $response['error'] = 'Служба CDEK не поддерживает партии!';

    }

}
