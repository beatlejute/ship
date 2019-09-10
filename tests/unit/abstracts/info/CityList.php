<?php

namespace tests\unit\abstracts\info;

abstract class cityList extends \Codeception\Test\Unit {

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

        $info = new $this->ClassName($this->authorization);

        $this->assertTrue($info instanceof \abstracts\info, 'Наследник класса info');

        $cityList = $info->cityList();

        $this->assertTrue(is_array($cityList), 'Проверяем, что возвращен массив');

        $this->assertEquals($cityList['error']['code'], "_1", 'Проверяем, что возвращено сообщение об ошибке про пустой ответ');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => "string"
        ]);

        $info = new $this->ClassName($this->authorization);

        $cityList = $info->cityList();

        $this->assertEquals($cityList['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "",
                    "kladr" => "99000000000",
                    "RegionName" => "",
                    "RegionTypeShort" => "",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $cityList = $info->cityList();

        $this->assertEquals($cityList['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле названия не заполнено');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Байконур",
                    "kladr" => "99000000000",
                    "RegionName" => "",
                    "RegionTypeShort" => "Байконур",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $cityList = $info->cityList();
        $this->assertFalse(isset($cityList['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "name" => "Байконур",
                    "kladr" => "99000000000",
                    "RegionName" => "",
                    "RegionTypeShort" => "Байконур",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $cityList = $info->cityList();

        //$this->assertEquals(print_r($cityList, true), "debug", 'debug');
        $this->assertEquals($cityList[0]['name'], "Байконур", 'Проверяем что город вернулся');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "Id" => 1,
                    "name" => "Байконур",
                    "kladr" => "99000000000",
                    "RegionName" => "Байконур",
                    "RegionTypeShort" => "",

                ),
                1 => array(

                    "Id" => 2,
                    "name" => "Екатеринбург",
                    "kladr" => "6600000100000",
                    "RegionName" => "Свердловская",
                    "RegionTypeShort" => "обл",

                ),
                2 => array(

                    "Id" => 3,
                    "name" => "Москва",
                    "kladr" => "7700000000000",
                    "RegionName" => "Москва",
                    "RegionTypeShort" => "",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $cityList = $info->cityList();

        $this->assertEquals($cityList[1]['name'], "Екатеринбург", 'Проверяем что второй город Екатеринбург');

        $cityList = $info->cityList("Екатеринбург");

        $this->assertEquals($cityList[0]['name'], "Екатеринбург", 'Проверяем что найденый по названию город Екатеринбург');

        $cityList = $info->cityList(false, false, "7700000000000");

        $this->assertEquals($cityList[0]['name'], "Москва", 'Проверяем что найденый по кладру город Москва');

        $cityList = $info->cityList("Екатеринбург", "Свердловская обл.");

        $this->assertEquals($cityList[0]['name'], "Екатеринбург", 'Проверяем что найденый по области город Екатеринбург');

        $cityList = $info->cityList("Екатеринбург", "Московская обл.");

        $this->assertEquals($cityList['error']['code'], "_2", 'Проверяем что Екатеринбург в МО не найден');

        $cityList = $info->cityList("Екатеринбург", false, 7700000000000);

        $this->assertEquals($cityList['error']['code'], "_2", 'Проверяем что Екатеринбург с кладр 7700000000000 не найден');

    }

}