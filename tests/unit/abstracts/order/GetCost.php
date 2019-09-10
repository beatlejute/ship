<?php

namespace tests\unit\abstracts\order;

abstract class getCost extends \Codeception\Test\Unit {

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


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "",
                    "mode" => "Д-Д",
                    "id" => 27,
                    "DPMin" => "3 рабочих дня",
                    "cost" => 219,
                    "info" => "К вам приедет курьер на указанный адрес и в указанную дату, заберет посылку и доставит до выбранного вами Пункта выдачи заказов, откуда уже самостоятельно заберет посылку получатель.",

                )

            ))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertEquals($getCost['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле названия не заполнено');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Доставка до двери",
                    "mode" => "Д-Д"
                )

            ))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertEquals($getCost['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле id не заполнено');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Доставка до двери",
                    "mode" => "Д-Д",
                    "id" => 27,
                    "DPMin" => "3 рабочих дня"
                )

            ))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertEquals($getCost['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле cost не заполнено');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Доставка до ПВЗ",
                    "mode" => "dpd",
                    "id" => 27,
                    "DPMin" => "3 рабочих дня",
                    "cost" => 219,
                    "info" => "К вам приедет курьер на указанный адрес и в указанную дату, заберет посылку и доставит до выбранного вами Пункта выдачи заказов, откуда уже самостоятельно заберет посылку получатель.",

                )

            ))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertEquals($getCost['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле mode не корректное');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Доставка до ПВЗ",
                    "mode" => "Д-Д",
                    "id" => 137,
                    "DPMin" => "3 рабочих дня",
                    "cost" => 219,
                    "info" => "К вам приедет курьер на указанный адрес и в указанную дату, заберет посылку и доставит до выбранного вами Пункта выдачи заказов, откуда уже самостоятельно заберет посылку получатель.",

                )

            ))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertFalse(isset($getCost['error']['code']), 'Нет сообщения об ошибке');
        //TODO: Проверка на неправильный код точки
    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Доставка до ПВЗ",
                    "mode" => "Д-Д",
                    "id" => 27,
                    "DPMin" => "3 рабочих дня",
                    "cost" => 219,
                    "info" => "К вам приедет курьер на указанный адрес и в указанную дату, заберет посылку и доставит до выбранного вами Пункта выдачи заказов, откуда уже самостоятельно заберет посылку получатель.",

                )

            ))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertEquals($getCost[0]['name'], "Доставка до ПВЗ", 'Проверяем что тариф вернулся');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode('[
                  {
                    "name": "DPD Дверь - Дверь",
                    "mode": "Д-Д",
                    "id": 26,
                    "DPMin": "3 рабочих дня",
                    "cost": 294,
                    "info": [
                      "dpd",
                      "К вам приедет курьер на указанный адрес и в указанную дату, заберет посылку и доставит до указанного адреса получателя."
                    ]
                  },
                  {
                    "name": "DPD Дверь - ПВЗ",
                    "mode": "Д-С",
                    "id": 27,
                    "DPMin": "3 рабочих дня",
                    "cost": 204,
                    "info": [
                      "dpd",
                      "К вам приедет курьер на указанный адрес и в указанную дату, заберет посылку и доставит до выбранного вами Пункта выдачи заказов, откуда уже самостоятельно заберет посылку получатель."
                    ]
                  },
                  {
                    "name": "DPD ПВЗ - ПВЗ",
                    "mode": "С-С",
                    "id": 29,
                    "DPMin": "3 рабочих дня",
                    "cost": 204,
                    "info": [
                      "dpd",
                      "Вы самостоятельно отнесете посылку в выбранный вами Пункт приема заказов, откуда посылка отправится до выбранного вами Пункта выдачи заказов, откуда уже самостоятельно заберет посылку получатель."
                    ]
                  },
                  {
                    "name": "DPD ПВЗ - Дверь",
                    "mode": "С-Д",
                    "id": 28,
                    "DPMin": "3 рабочих дня",
                    "cost": 294,
                    "info": [
                      "dpd",
                      "Вы самостоятельно отнесете посылку в выбранный вами Пункт приема заказов, откуда посылка отправится до указанного адреса получателя."
                    ]
                  },
                  {
                    "name": "CDEK ПВЗ-ПВЗ",
                    "mode": "С-С",
                    "id": 57,
                    "DPMin": "3 рабочих дня",
                    "cost": 317.5,
                    "info": [
                      "cdek",
                      null
                    ]
                  },
                  {
                    "name": "CDEK Дверь-ПВЗ",
                    "mode": "Д-С",
                    "id": 59,
                    "DPMin": "3 рабочих дня",
                    "cost": 457.5,
                    "info": [
                      "cdek",
                      null
                    ]
                  },
                  {
                    "name": "CDEK ПВЗ-Дверь",
                    "mode": "С-Д",
                    "id": 58,
                    "DPMin": "3 рабочих дня",
                    "cost": 487.5,
                    "info": [
                      "cdek",
                      null
                    ]
                  },
                  {
                    "name": "CDEK Дверь-Дверь",
                    "mode": "Д-Д",
                    "id": 60,
                    "DPMin": "3 рабочих дня",
                    "cost": 627.5,
                    "info": [
                      "cdek",
                      null
                    ]
                  }
                ]', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $getCost = $order->getCost('Москва', 'Московская обл.', 'Екатеринбург');

        $this->assertEquals($getCost[2]['name'], "DPD ПВЗ - ПВЗ", 'Проверяем что третий тариф DPD ПВЗ - ПВЗ');

        $getCost = $order->getCost('Москва', 'Московская обл.', null, null, '123');

        $this->assertEquals($getCost[0]['name'], "DPD Дверь - ПВЗ", 'Проверяем что найденый по ПВЗ тариф DPD Дверь - ПВЗ');

        /* TODO: Проверка получения города по индексу */

    }

}