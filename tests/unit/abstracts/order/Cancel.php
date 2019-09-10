<?php

namespace tests\unit\abstracts\order;

abstract class cancel extends \Codeception\Test\Unit {

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


        $cancel = $order->cancel();

        $this->assertEquals($cancel['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про отсутствие входных данных.');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(null, null)
        ]);

        $order = new $this->ClassName($this->authorization);

        $cancel = $order->cancel('НФ-1234');

        $this->assertFalse(isset($cancel['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(null, null)
        ]);

        $order = new $this->ClassName($this->authorization);

        $cancel = $order->cancel(null, '744171');

        $this->assertTrue($cancel['Result'], 'Проверяем что результат положительный');

    }

}