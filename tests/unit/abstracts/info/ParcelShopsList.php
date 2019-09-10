<?php
namespace tests\unit\abstracts\info;

abstract class parcelShopsList extends \Codeception\Test\Unit {

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

        $parcelShopsList = $info->parcelShopsList();

        $this->assertTrue(is_array($parcelShopsList), 'Проверяем, что возвращен массив');

        $this->assertEquals($parcelShopsList['error']['code'], "_1", 'Проверяем, что возвращено сообщение об ошибке про пустой ответ');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => "string"
        ]);

        $info = new $this->ClassName($this->authorization);

        $parcelShopsList = $info->parcelShopsList();

        $this->assertEquals($parcelShopsList['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "Number" => NULL,
                    "OwnerName" => "boxberry",
                    "Address" => "620026, Екатеринбург г, Мамина-Сибиряка ул, д.130",
                    "Phone" => "+7(343)361-73-82",
                    "OutDescription" => "Перекресток Мамина-Сибиряка\/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                    "WorkTime" => "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                    "Card" => true,
                    "Latitude" => "56.830944",
                    "Longitude" => "60.620520",
                    "MaxWeight" => 31,
                    "MaxSize" => 31,
                    "Region" => "Свердловская обл",
                    "CitiName" => "Екатеринбург",
                    "Street" => "ул Мамина-Сибиряка",
                    "House" => "130",
                    "Index" => "620026",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $parcelShopsList = $info->parcelShopsList();

        $this->assertEquals($parcelShopsList['error']['code'], "_2", 'Проверяем, что возвращено сообщение об ошибке про некорректный ответ если поле number не заполнено');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "Number" => 55,
                    "OwnerName" => "boxberry",
                    "Address" => "620026, Екатеринбург г, Мамина-Сибиряка ул, д.130",
                    "Phone" => "+7(343)361-73-82",
                    "OutDescription" => "Перекресток Мамина-Сибиряка\/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                    "WorkTime" => "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                    "Card" => true,
                    "Latitude" => "56.830944",
                    "Longitude" => "60.620520",
                    "MaxWeight" => 31,
                    "MaxSize" => 31,
                    "Region" => "Свердловская обл",
                    "CitiName" => "Екатеринбург",
                    "Street" => "ул Мамина-Сибиряка",
                    "House" => "130",
                    "Index" => "620026",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $parcelShopsList = $info->parcelShopsList();

        $this->assertFalse(isset($parcelShopsList['error']['code']), 'Нет сообщения об ошибке');

    }

    public function testReturns() {

        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "Number" => 55,
                    "OwnerName" => "boxberry",
                    "Address" => "620026, Екатеринбург г, Мамина-Сибиряка ул, д.130",
                    "Phone" => "+7(343)361-73-82",
                    "OutDescription" => "Перекресток Мамина-Сибиряка\/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                    "WorkTime" => "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                    "Card" => true,
                    "Latitude" => "56.830944",
                    "Longitude" => "60.620520",
                    "MaxWeight" => 31,
                    "MaxSize" => 31,
                    "Region" => "Свердловская обл",
                    "CitiName" => "Екатеринбург",
                    "Street" => "ул Мамина-Сибиряка",
                    "House" => "130",
                    "Index" => "620026",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $parcelShopsList = $info->parcelShopsList();

        $this->assertEquals($parcelShopsList[0]['Number'], 55, 'Проверяем что ПВЗ вернулся');



        $this->authorization = $this->make('\\'.$this->namespace.'\\authorization', [
            'query' => $this->_mackInterface(array(

                0 => array(

                    "Number" => 55,
                    "OwnerName" => "boxberry",
                    "Address" => "620026, Екатеринбург г, Мамина-Сибиряка ул, д.130",
                    "Phone" => "+7(343)361-73-82",
                    "OutDescription" => "Перекресток Мамина-Сибиряка\/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                    "WorkTime" => "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                    "Card" => false,
                    "Latitude" => "56.830944",
                    "Longitude" => "60.620520",
                    "MaxWeight" => 30,
                    "MaxSize" => 30,
                    "Region" => "Свердловская обл.",
                    "CitiName" => "Екатеринбург",
                    "Street" => "ул Мамина-Сибиряка",
                    "House" => "130",
                    "Index" => "620026",

                ),
                1 => array(

                    "Number" => 38,
                    "OwnerName" => "boxberry",
                    "Address" => "105264, Москва г, Измайловский б-р, д.43",
                    "Phone" => "+7(499)391-56-22",
                    "OutDescription" => "Перекресток Мамина-Сибиряка\/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                    "WorkTime" => "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                    "Card" => false,
                    "Latitude" => "56.830944",
                    "Longitude" => "60.620520",
                    "MaxWeight" => 20,
                    "MaxSize" => 20,
                    "Region" => "Москва",
                    "CitiName" => "Москва",
                    "Street" => "ул Мамина-Сибиряка",
                    "House" => "130",
                    "Index" => "620026",

                ),
                2 => array(

                    "Number" => 163,
                    "OwnerName" => "dpd",
                    "Address" => "420006 Татарстан, Казань, улица Хлебозаводская, 7В",
                    "Phone" => "+7(343)361-73-82",
                    "OutDescription" => "Перекресток Мамина-Сибиряка\/Куйбышева. \nОтделение находится в красном кирпичном жилом доме (ближе к Куйбышева)\nСо стороны ул. К.Маркса - двигаться вдоль длинного здания из красного кирпича, пройти вход в отделение Сбербанка, на Почту России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).\nСо стороны Куйбышева - двигаться вдоль длинного здания из красного кирпича, второе крыльцо, не доходя до отделения Сбербанка, Почты России. \nВход в отделение  - отдельное крыльцо (на окнах вывески Боксберри).",
                    "WorkTime" => "пн-пт:10.00-20.00, сб-вс:10.00-16.00",
                    "Card" => true,
                    "Latitude" => "56.830944",
                    "Longitude" => "60.620520",
                    "MaxWeight" => 10,
                    "MaxSize" => 10,
                    "Region" => "Респ Татарстан",
                    "CitiName" => "Казань",
                    "Street" => "ул Мамина-Сибиряка",
                    "House" => "130",
                    "Index" => "620026",

                )

            ))
        ]);

        $info = new $this->ClassName($this->authorization);

        $parcelShopsList = $info->parcelShopsList();

        $this->assertEquals($parcelShopsList[1]['CitiName'], "Москва", 'Проверяем, что второй город Екатеринбург');

        $parcelShopsList = $info->parcelShopsList("Москва");

        $this->assertEquals($parcelShopsList[0]['CitiName'], "Москва", 'Проверяем, что найденый по названию город Москва');

        $parcelShopsList = $info->parcelShopsList(false, "Свердловская обл.");

        $this->assertEquals($parcelShopsList[0]['CitiName'], "Екатеринбург", 'Проверяем, что найденый по региону город Екатеринбург');
        $this->assertEquals(sizeof($parcelShopsList), 1, 'Проверяем, что найден по региону только один город');

        $parcelShopsList = $info->parcelShopsList("Екатеринбург", "Свердловская обл.");

        $this->assertEquals($parcelShopsList[0]['CitiName'], "Екатеринбург", 'Проверяем что найденый по области город Екатеринбург');

        $parcelShopsList = $info->parcelShopsList("Екатеринбург", "Московская обл.");

        $this->assertEquals($parcelShopsList['error']['code'], "_2", 'Проверяем что Екатеринбург в МО не найден');

        $parcelShopsList = $info->parcelShopsList(false, false, 5);

        $this->assertEquals(sizeof($parcelShopsList), 3, 'Проверяем, что найденыы все ПВЗ на вес 5 кг.');

        $parcelShopsList = $info->parcelShopsList(false, false, 15);

        $this->assertEquals(sizeof($parcelShopsList), 2, 'Проверяем, что найденыы 2 ПВЗ на вес 15 кг.');

        $parcelShopsList = $info->parcelShopsList(false, false, 25);

        $this->assertEquals(sizeof($parcelShopsList), 1, 'Проверяем, что найденыы 2 ПВЗ на вес 25 кг.');

        $parcelShopsList = $info->parcelShopsList(false, false, false, 5);

        $this->assertEquals(sizeof($parcelShopsList), 3, 'Проверяем, что найденыы все ПВЗ на объем 5 см.');

        $parcelShopsList = $info->parcelShopsList(false, false, false, 15);

        $this->assertEquals(sizeof($parcelShopsList), 2, 'Проверяем, что найденыы 2 ПВЗ на объем 15 см.');

        $parcelShopsList = $info->parcelShopsList(false, false, false, 25);

        $this->assertEquals(sizeof($parcelShopsList), 1, 'Проверяем, что найденыы 2 ПВЗ на объем 25 см.');

        $parcelShopsList = $info->parcelShopsList(false, false, false, false, 'card');

        $this->assertEquals($parcelShopsList[0]['CitiName'], "Казань", 'Проверяем, что найденый по способу оплаты ПВЗ в Казани');


        $parcelShopsList = $info->parcelShopsList(false, false, false, false, false, 38);

        $this->assertEquals($parcelShopsList[0]['CitiName'], "Москва", 'Проверяем, что найденый по ИД ПВЗ в Москве');

    }

}