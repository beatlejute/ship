<?php

namespace tests\unit\abstracts\order;

abstract class create extends \Codeception\Test\Unit {

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

        $create = $order->create(null);

        $this->assertEquals($create['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про пустое тело запроса.');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode('{
                "CreatedSendings": [
                    {
                        "EDTN": "1",
                        "InvoiceNumber": "11111",
                        "Barcode": "123456",
                        "orderNumber": "НФ-1234"
                    }
                ],
                "RejectedSendings": []
            }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $create = $order->create(json_decode('[
            {
                "orderNumber": "НФ-1234",
                "description": "тест",
                "declaredValue": "1000",
                "tariff": 1,
                "Pickup": {
                    "fromCity": "Москва",
                    "fromRegion": "Московская обл.",
                    "selfPickup": true
                },
                "payOnDelivery": true,
                "payment": "card",
                "recipient": {
                    "name": "Тестовый клиент",
                    "phone": "89120000000",
                    "email": "test@test.test"
                },
                "toPoint": "6602-007",
                "cargoCount": 1,
                "Places": [
                    {
                        "Width": 10,
                        "Height": 10,
                        "Depth": 10,
                        "Weight": 1,
                        "SubEncloses": [
                            {
                                "GoodsCode": 123,
                                "Price": 1000,
                                "Weight": 10,
                                "count": 1,
                                "name": "Тест"
                            }
                        ]
                    }
                ]
            }
        ]', true));

        $this->assertEquals($create['CreatedSendings'][0]["orderNumber"], "НФ-1234", 'Проверяем, что возвращено сообщение об успешном создании заказа');

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode('{
                "CreatedSendings": [
                    {
                        "EDTN": "1",
                        "InvoiceNumber": "11111",
                        "Barcode": "123456",
                        "orderNumber": "НФ-1234"
                    },
                    {
                        "EDTN": "2",
                        "InvoiceNumber": "11112",
                        "Barcode": "123457",
                        "orderNumber": "НФ-1235"
                    }
                ],
                "RejectedSendings": []
            }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $create = $order->create(json_decode('[
            {
                "orderNumber": "НФ-1234",
                "description": "тест",
                "declaredValue": "1000",
                "tariff": 1,
                "Pickup": {
                    "fromCity": "Москва",
                    "fromRegion": "Московская обл.",
                    "selfPickup": true
                },
                "payOnDelivery": true,
                "payment": "card",
                "recipient": {
                    "name": "Тестовый клиент",
                    "phone": "89120000000",
                    "email": "test@test.test"
                },
                "toPoint": "6602-007",
                "cargoCount": 1,
                "Places": [
                    {
                        "Width": 10,
                        "Height": 10,
                        "Depth": 10,
                        "Weight": 1,
                        "SubEncloses": [
                            {
                                "GoodsCode": 123,
                                "Price": 1000,
                                "Weight": 10,
                                "count": 1,
                                "name": "Тест"
                            }
                        ]
                    }
                ]
            },
            {
                "orderNumber": "НФ-1235",
                "description": "тест",
                "declaredValue": "1000",
                "tariff": 1,
                "Pickup": {
                    "fromCity": "Москва",
                    "fromRegion": "Московская обл.",
                    "selfPickup": true
                },
                "payOnDelivery": true,
                "payment": "card",
                "recipient": {
                    "name": "Тестовый клиент",
                    "phone": "89120000000",
                    "email": "test@test.test"
                },
                "toPoint": "6602-007",
                "cargoCount": 1,
                "Places": [
                    {
                        "Width": 10,
                        "Height": 10,
                        "Depth": 10,
                        "Weight": 1,
                        "SubEncloses": [
                            {
                                "GoodsCode": 123,
                                "Price": 1000,
                                "Weight": 10,
                                "count": 1,
                                "name": "Тест"
                            }
                        ]
                    }
                ]
            }
        ]', true));

        $this->assertEquals($create['CreatedSendings'][1]["InvoiceNumber"], "11112", 'Проверяем, что возвращено сообщение об успешном создании заказа');

    }

}