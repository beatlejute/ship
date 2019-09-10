<?php

namespace tests\unit\abstracts\order;

abstract class getInfo extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $authorization;
    protected $namespace;
    protected $ClassName;

    protected function _before() {
    }

    protected function _after() {
    }

    protected function _mackInterface($obj) {

        return $obj;

    }

    // tests
    public function testException() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => NULL
        ]);

        $order = new $this->ClassName($this->authorization);

        $this->assertTrue($order instanceof \abstracts\order, 'Наследник класса order');


        $getInfo = $order->getInfo();

        $this->assertEquals($getInfo['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про отсутствие входных данных.');


        $getInfo = $order->getInfo('НФ-1234');

        $this->assertEquals($getInfo['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если не найдено такое отправление.');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode(
                '{
                      "status": {
                        "State": "",
                        "StateMessage": "Новая"
                      },
                      "info": {
                        "InvoiceNumber": 744171,
                        "SenderInvoiceNumber": "НФ-1234",
                        "Sum": 1000,
                        "CreateDate": "2018-11-22 09:50:34",
                        "FIO": "Тестовый клиент",
                        "StorageDate": null,
                        "tariff": 14,
                        "address": {
                          "receiver": "Тестовый клиент",
                          "email": "test@test.test",
                          "phone": "+7 912 000-00-00",
                          "country_code": "RU",
                          "administrative_area": "Свердловская обл",
                          "settlement": "г Екатеринбург",
                          "address_line_1": null,
                          "marked_as_trash_at": null,
                          "name": null,
                          "surname": null,
                          "patronymic": null,
                          "postal_code": "620026",
                          "street": "ул Мамина-Сибиряка",
                          "house": "130",
                          "apartment": null,
                          "kladr_id": "66000001000"
                        }
                      }
                    }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getInfo = $order->getInfo('НФ-1234');

        $this->assertEquals($getInfo['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле статуса не заполнено');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode(
                '{
                      "status": {
                        "State": "new",
                        "StateMessage": "Новая"
                      },
                      "info": {
                        "InvoiceNumber": 744171,
                        "SenderInvoiceNumber": "НФ-1234",
                        "Sum": 1000,
                        "CreateDate": "2018-11-22 09:50:34",
                        "FIO": "Тестовый клиент",
                        "StorageDate": null,
                        "tariff": 14,
                        "address": {
                          "receiver": "Тестовый клиент",
                          "email": "test@test.test",
                          "phone": "+7 912 000-00-00",
                          "country_code": "RU",
                          "administrative_area": "Свердловская обл",
                          "settlement": "г Екатеринбург",
                          "address_line_1": null,
                          "marked_as_trash_at": null,
                          "name": null,
                          "surname": null,
                          "patronymic": null,
                          "postal_code": "620026",
                          "street": "ул Мамина-Сибиряка",
                          "house": "130",
                          "apartment": null,
                          "kladr_id": "66000001000"
                        }
                      }
                    }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getInfo = $order->getInfo('НФ-1234');

        $this->assertFalse(isset($getInfo['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode(
                '{
                      "status": {
                        "State": "new",
                        "StateMessage": "Новая"
                      },
                      "info": {
                        "InvoiceNumber": 744171,
                        "SenderInvoiceNumber": "НФ-1234",
                        "Sum": 1000,
                        "CreateDate": "2018-11-22 09:50:34",
                        "FIO": "Тестовый клиент",
                        "StorageDate": null,
                        "tariff": 14,
                        "address": {
                          "receiver": "Тестовый клиент",
                          "email": "test@test.test",
                          "phone": "+7 912 000-00-00",
                          "country_code": "RU",
                          "administrative_area": "Свердловская обл",
                          "settlement": "г Екатеринбург",
                          "address_line_1": null,
                          "marked_as_trash_at": null,
                          "name": null,
                          "surname": null,
                          "patronymic": null,
                          "postal_code": "620026",
                          "street": "ул Мамина-Сибиряка",
                          "house": "130",
                          "apartment": null,
                          "kladr_id": "66000001000"
                        }
                      }
                    }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getInfo = $order->getInfo(null, '744171');

        $this->assertEquals($getInfo['status']['State'], "new", 'Проверяем что статус new');

        $this->assertEquals($getInfo['info']['SenderInvoiceNumber'], "НФ-1234", 'Проверяем что номер НФ-1234');

        $this->assertEquals($getInfo['info']['InvoiceNumber'], 744171, 'Проверяем что номер ТК 744171');

    }

}