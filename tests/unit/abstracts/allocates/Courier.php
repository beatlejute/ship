<?php

namespace tests\unit\abstracts\allocates;

abstract class courier extends \Codeception\Test\Unit {

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

        $allocates = new $this->ClassName($this->authorization);

        $this->assertTrue($allocates instanceof \abstracts\allocates, 'Наследник класса allocates');


        $courier = $allocates->courier(null, null, null);

        $this->assertEquals($courier['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про отсутствие входных данных.');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(null, null)
        ]);

        $allocates = new $this->ClassName($this->authorization);

        $courier = $allocates->courier('Тестовый клиент', '+7 (000) 00-00-00', '31.05.2017');

        $this->assertFalse(isset($courier['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(null, null)
        ]);

        $allocates = new $this->ClassName($this->authorization);

        $courier = $allocates->courier('Тестовый клиент', '+7 (000) 00-00-00', '31.05.2017');

        $this->assertTrue($courier['CourierRequestRegistred'], 'Проверяем что результат положительный');

    }

}