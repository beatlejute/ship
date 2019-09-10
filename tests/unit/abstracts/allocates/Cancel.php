<?php

namespace tests\unit\abstracts\allocates;

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

        $allocates = new $this->ClassName($this->authorization);

        $this->assertTrue($allocates instanceof \abstracts\allocates, 'Наследник класса allocates');


        $cancel = $allocates->cancel(null);

        $this->assertEquals($cancel['error']['code'], "_3", 'Проверяем, что возвращено сообщение об ошибке про отсутствие входных данных.');


        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(null, null)
        ]);

        $allocates = new $this->ClassName($this->authorization);

        $cancel = $allocates->cancel('262047');

        $this->assertFalse(isset($cancel['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(null, null)
        ]);

        $allocates = new $this->ClassName($this->authorization);

        $cancel = $allocates->cancel('262047');

        $this->assertTrue($cancel['Canceled'], 'Проверяем что результат положительный');

    }

}