<?php

namespace dpd;

class batches extends \abstracts\batches {

    public function getOrderList($dateFrom, $dateTo) { //Отбор списка заказов за временной интервал

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;



        $query['dateFrom'] = date('c', strtotime($dateFrom));
        $query['dateTo'] = date('c', strtotime($dateTo." 23:59"));
        $query['maxRowCount'] = 1000;

        $request = $this->authorization->query($query, "event-tracking?wsdl", "getEvents", 'request');
        if($request['error']) return ['error' => $request['error']];

        if(!$request) {

            $answer['error']['code'] = "_1";
            $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

            return $answer;

        }
        if(!is_array($request)) {

            $answer['error']['code'] = "_2";
            $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
            $answer['error']['info'][] = print_r($request, true);

            return $answer;

        }


        foreach($request['event'] as $orderId => $order) {

            $response['Invoices'][$orderId]['CustomerNumber'] = $order['clientOrderNr'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Code'] = $order['eventCode'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Description'] = $order['eventName'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Modified'] =  date("d.m.Y H:i:s", strtotime($order['eventDate']));

            /*foreach($order['history'] as $historyId => $history) {

                $response['Invoices'][$orderId]['Encloses']['Statuses'][$historyId]['Description'] = $history['event'].' '.$history['description'];
                $response['Invoices'][$orderId]['Encloses']['Statuses'][$historyId]['Modified'] =  date("d.m.Y H:i:s", strtotime($history['date']));

            }*/

            $response['Invoices'][$orderId]['Number'] = $order['dpdOrderNr'];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
            5.4.6.	getEvents
                Параметр	                Описание	                Тип	            Обязательный	        Пример
                Внешний тэг	request
                auth
                            clientNumber	Ваш клиентский номер в
                                            системе DPD (номер вашего
                                            договора с DPD)	            Число	        Да	                    1000000000
                            clientKey	    Ваш уникальный ключ для
                                            авторизации, полученный у
                                            сотрудника DPD	            Строка	        Да	                    1FD890C3556
                dateTo	                    Дата статуса заказа. Если
                                             не заполнено, то текущая
                                            дата	                    Дата	        Нет	                    2016-04-07T00:00:00+03:00
                dateFrom	                Дата статуса заказа.Если
                                            не заполнено, то текущая
                                            дата минус 15 календарных
                                            дней.	                    Дата	        Нет	                    2016-04-07T00:00:00+03:00
                maxRowCount	                Максимальное количество
                                            записей передаваемое в
                                            ответе. Если не заполнено
                                            то 50.	                    Число	        Нет


        5.5.2.	getEventsResponse
            http://ws.dpd.ru/services/event-tracking?xsd=1
                Параметр	                Описание	                Тип	            Пример
                docId	                    Идентификатор документа.
                                            Данный идентификатор
                                            используется для
                                            подтверждения получения
                                            статусов	                Число	        12346897
                docDate	                    Дата формирования
                                            документа	                Дата	        2014-02-28
                clientNumber	            Ваш клиентский номер в
                                            системе DPD	                Число	        1000000000
                resultComplete	            Показывает, выбраны ли в
                                            текущем запросе все
                                            новые состояния по
                                            клиенту (значение true),
                                            или был достигнут
                                            лимит записей в
                                            одном запросе и
                                            для продолжения
                                            необходим ещё один
                                            запрос (значение false).
                                            Пояснение.
                                            Если возвращается false,
                                            то значит есть ещё статусы
                                            и можно повторно вызвать
                                            метод, чтобы их получить.
                                            В этом случае нет
                                            ограничения на повторный
                                            вызов.
                                            Если же в ответе вернулось
                                            true – то значит больше
                                            статусов нет и повторный
                                            вызов возможен только
                                            через 5мин.
                                                                        boolean 	    true

                    clientOrderNr	        Номер заказа в
                                            информационной системе
                                            клиента	                    Строка	        12346DPD
                    dpdOrderNr	            Номер заказа в
                                            информационной системе
                                            DPD	                        Строка	        04040001MOW
                    eventNumber	            Номер условий статуса	    Строка
                    eventCode	            Если EventCode пуст,
                                            то заполняется TypeCode	    Строка
                    eventName	            Наименование статуса	    Строка
                    eventName	            Наименование статуса	    Строка
                    reasonCode	            Код причины статуса	        Строка
                    reasonName	            Наименование
                                            причины статуса	            Строка
                    EventDate	            Момент события	            Дата/Время
                    parameter	            Тип parameter	            Массив

        */
    }
    public function getLabel($putdata='', $dateFrom='', $dateTo='') { //Печать Наклеек

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;



        $query['fileFormat'] = 'PDF';
        $query['pageSize'] = 'A5';

        require_once 'utils/mpdf/vendor/autoload.php';

        $mpdf = new \mPDF();

        foreach($orders as $orderId => $order) if($order) {

            $query['order']['orderNum'] = $orders[$orderId];
            $query['order']['parcelsNumber'] = 1;

            $request = $this->authorization->query($query, "label-print?wsdl", "createLabelFile", 'getLabelFile');
            if($request['error']) return ['error' => $request['error']];

            if($request['file']) {

                $tmpFile = fopen('/tmp/'.$orders[$orderId].'.pdf', 'w');
                fwrite($tmpFile, $request['file']);
                fclose($tmpFile);

                $mpdf->AddPage();
                $mpdf->SetImportUse();
                $pagecount = $mpdf->SetSourceFile($tmpFile);
                for ($i=1; $i<=$pagecount; $i++) {
                    $import_page = $mpdf->ImportPage($i);
                    $mpdf->UseTemplate($import_page);

                    if ($i < $pagecount)
                        $mpdf->AddPage();
                }

                //unlink($tmpFile);

            }

        }



        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        $mpdf->WriteHTML('<pagebreak />');
        $mpdf->Output();

        die();

        //return $response;

        /*
       7.5.1.	createLabelFile
            Параметр	                        Описание	            Тип	                Обязательный	            Пример
            Внешний тэг	getLabelFile
            Auth
                            clientNumber	    Ваш клиентский номер в
                                                системе DPD (номер
                                                вашего договора с DPD)	Число	            Да	                        1000000000
                            clientKey	        Ваш уникальный ключ для
                                                авторизации, полученный
                                                у сотрудника DPD	    Строка	            Да	                        1FD890C3556
            fileFormat		                    Формат файла.
                                                Возможные значения:
                                                PDF, FP3	            Срока               Да	                        PDF
            pageSize		                    Формат области печати.
                                                Возможные значения:
                                                A5, A6.		                                Да	                        A5
            order		                        Массив данных,
                                                относящихся к каждому
                                                конкретному заказу
                            orderNum	        Номер заказа DPD	    Строка	            Да	                        05120002MOW
                            parcelsNumber	    Кол-во наклеек для
                                                формирования	        Число	            Да	                        3



        7.6.1.	createLabelFileResponse
            Параметр	                        Описание	            Тип	                Обязательный	            Пример
            file		                        Файл	                Массив байтов
            order		                        Массив данных,
                                                относящихся к каждому
                                                конкретному заказу
                            orderNum	        Номер заказа DPD	    Строка		                                    05120002MOW
                            status	            Статус	                Строка		                                    OrderPending
                            errorMessage	    Сообщение об ошибке	    Строка		                                    Заказ 95890002690 для клиента 1001000000 не найден

        */

    }
    public function create($orders='', $dateFrom='', $dateTo='') { //Создаёт партию

        return $response['error'] = 'Служба dpd не поддерживает партии!';

    }
    public function getInfo($invoiceNumber='', $reestrNumber='', $pickupDate='') { //Запрашивает данные об партиях

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;



        $query['datePickup'] = date("Y-m-d", strtotime($pickupDate));

        $request = $this->authorization->query($query, "order2?wsdl", "getRegisterFile", 'request');
        if($request['error']) return ['error' => $request['error']];

        $response = $request['file'];



        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;


        header("Content-Type: application/vnd.ms-excel");


        print $response;

        /*
         3.5.8.	Параметры входного сообщения getRegisterFile
            Параметр	                Описание	            Тип	                Обязательный	            Пример
            Внешний тэг	request
            auth
                        clientNumber	Ваш клиентский номер в
                                        системе DPD (номер
                                        вашего договора с
                                        DPD)	                Число	            Да	                        1000000000
                        clientKey	    Ваш уникальный ключ
                                        для авторизации,
                                        полученный у сотрудника
                                        DPD	                    Строка	            Да	                        1FD890C3556
            datePickup		            Дата приёма груза (на
                                        тот случай, если
                                        номер в вашей
                                        информационной системе
                                        не является
                                        уникальным)	            Дата	            Да	                        2014-09-15
            regularNum		            Номер регулярного
                                        заказа DPD. Если вы
                                        используете доставку
                                        на регулярной основе,
                                        уточните этот
                                        номер у своего
                                        менеджера.	            Строка	            Нет	                        1000
            cityPickupId		        Идентификатор города
                                        приёма груза в
                                        системе DPD	            Строка	            Нет	                        123456
            addressCode		            Код адреса в
                                        информационных системах
                                        заказчика и DPD.
                                        Адрес с кодом
                                        должен быть передан
                                        в DPD отдельно.	        Строка	            Нет	                        1234



        3.5.9.	Параметры ответного сообщения getRegisterFile
            Параметр	                Описание	            Тип	                Обязательный	            Пример
            file	                    Файл	                Массив байтов	    Да

         */

    }
    public function removeOrder($orderNumber='', $invoiceNumber='') { //Исключение заказа на доставку из всех партий

        return $response['error'] = 'Служба dpd не поддерживает партии!';

    }

}
