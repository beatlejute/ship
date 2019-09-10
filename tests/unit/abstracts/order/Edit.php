<?php

namespace tests\unit\abstracts\order;

abstract class edit extends \Codeception\Test\Unit {

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


        $edit = $order->edit(null);

        $this->assertEquals($edit['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про отсутствие входных данных.');


        $edit = $order->edit('НФ-1234');

        $this->assertEquals($edit['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если не найдено такое отправление.');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode(
                '{
                  "InvoiceNumber": null,
                  "GCInvoiceNumber": "НФ-1234"
                }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $edit = $order->edit('НФ-1234');

        $this->assertEquals($edit['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле статуса не заполнено');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode(
                '{
                  "InvoiceNumber": 744171,
                  "GCInvoiceNumber": "НФ-1234"
                }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $edit = $order->edit('НФ-1234');

        $this->assertFalse(isset($edit['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(json_decode(
                '{
                  "InvoiceNumber": 744171,
                  "GCInvoiceNumber": "НФ-1234"
                }', true))
        ]);

        $order = new $this->ClassName($this->authorization);

        $edit = $order->edit(null, '744171');


        $this->assertEquals($edit['GCInvoiceNumber'], "НФ-1234", 'Проверяем что номер НФ-1234');

        $this->assertEquals($edit['InvoiceNumber'], 744171, 'Проверяем что номер ТК 744171');

    }

}