<?php

namespace tests\unit\abstracts\batches;

abstract class getOrderList extends \Codeception\Test\Unit {

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

        $batches = new $this->ClassName($this->authorization);

        $this->assertTrue($batches instanceof \abstracts\batches, 'Наследник класса batches');

        /*$getOrderList = $batches->getOrderList();

        $this->assertEquals($getOrderList['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про отсутствие входных данных.');*/


        $getOrderList = $batches->getOrderList('01.11.2018', null);

        $this->assertTrue(is_array($getOrderList), 'Проверяем, что возвращен массив');

        $this->assertEquals($getOrderList['error']['code'], "_1", 'Проверяем, что возвращено сообщение об ошибке про пустой ответ');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => "string"
        ]);

        $batches = new $this->ClassName($this->authorization);

        $getOrderList = $batches->getOrderList('01.11.2018', null);

        $this->assertEquals($getOrderList['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(
                json_decode('{
                                    "Invoices": [
                                        {
                                            "CustomerNumber": "",
                                            "Encloses": {
                                                "Statuses": [
                                                    {
                                                        "Code": "new",
                                                        "Description": "Создана проблема ",
                                                        "Modified": "03.12.2018 07:26:54"
                                                    },
                                                    {
                                                        "Description": "Адрес изменен ",
                                                        "Modified": "03.12.2018 07:26:53"
                                                    },
                                                    {
                                                        "Description": "Адрес изменен ",
                                                        "Modified": "03.12.2018 07:26:53"
                                                    },
                                                    {
                                                        "Description": "Посылка создана ",
                                                        "Modified": "03.12.2018 07:26:52"
                                                    }
                                                ]
                                            },
                                            "Number": 769787
                                        }
                                    ]
                                }', true))
        ]);

        $batches = new $this->ClassName($this->authorization);

        $getOrderList = $batches->getOrderList('01.11.2018', null);

        $this->assertEquals($getOrderList['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле названия не заполнено');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(
                json_decode('{
                                "Invoices": [
                                    {
                                        "CustomerNumber": "НФ-1235",
                                        "Encloses": {
                                            "Statuses": [
                                                {
                                                    "Code": "new",
                                                    "Description": "Создана проблема ",
                                                    "Modified": "03.12.2018 07:26:54"
                                                },
                                                {
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "03.12.2018 07:26:53"
                                                },
                                                {
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "03.12.2018 07:26:53"
                                                },
                                                {
                                                    "Description": "Посылка создана ",
                                                    "Modified": "03.12.2018 07:26:52"
                                                }
                                            ]
                                        },
                                        "Number": 769787
                                    },
                                    {
                                        "CustomerNumber": "НФ-1234",
                                        "Encloses": {
                                            "Statuses": [
                                                {
                                                    "Code": "new",
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "22.11.2018 09:50:37"
                                                },
                                                {
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "22.11.2018 09:50:35"
                                                },
                                                {
                                                    "Description": "Посылка создана ",
                                                    "Modified": "22.11.2018 09:50:34"
                                                }
                                            ]
                                        },
                                        "Number": 744171
                                    }
                                ]
                            }', true))
        ]);

        $batches = new $this->ClassName($this->authorization);

        $getOrderList = $batches->getOrderList('01.11.2018', null);
        $this->assertFalse(isset($getOrderList['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(
                json_decode('{
                                "Invoices": [
                                    {
                                        "CustomerNumber": "НФ-1235",
                                        "Encloses": {
                                            "Statuses": [
                                                {
                                                    "Code": "new",
                                                    "Description": "Создана проблема ",
                                                    "Modified": "03.12.2018 07:26:54"
                                                },
                                                {
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "03.12.2018 07:26:53"
                                                },
                                                {
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "03.12.2018 07:26:53"
                                                },
                                                {
                                                    "Description": "Посылка создана ",
                                                    "Modified": "03.12.2018 07:26:52"
                                                }
                                            ]
                                        },
                                        "Number": 769787
                                    },
                                    {
                                        "CustomerNumber": "НФ-1234",
                                        "Encloses": {
                                            "Statuses": [
                                                {
                                                    "Code": "new",
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "22.11.2018 09:50:37"
                                                },
                                                {
                                                    "Description": "Адрес изменен ",
                                                    "Modified": "22.11.2018 09:50:35"
                                                },
                                                {
                                                    "Description": "Посылка создана ",
                                                    "Modified": "22.11.2018 09:50:34"
                                                }
                                            ]
                                        },
                                        "Number": 744171
                                    }
                                ]
                            }', true))
        ]);

        $batches = new $this->ClassName($this->authorization);

        $getOrderList = $batches->getOrderList(null, null);

        $this->assertEquals($getOrderList['Invoices'][0]['CustomerNumber'], "НФ-1235", 'Проверяем что номер заказа НФ-1235');

        $this->assertEquals($getOrderList['Invoices'][1]['Number'], 744171, 'Проверяем что номер заказа ТК 744171');

        $this->assertEquals($getOrderList['Invoices'][1]['Encloses']['Statuses'][0]['Code'], "new", 'Проверяем что статус new');

    }

}