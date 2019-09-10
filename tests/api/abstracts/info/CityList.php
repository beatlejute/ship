<?php
namespace tests\api\abstracts\info;

class cityList
{

    protected $namespace;

    public function _before(\ApiTester $I)
    {
    }

    // tests
    public function getCityListTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/cityList/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => 'Москва'));
    }
    public function getCityListByNameTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/cityList/?cityName=Екатеринбург');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => 'Екатеринбург'));
        $I->seeResponseContainsJson(array('kladr' => '6600000100000'));
        $I->seeResponseContainsJson(array('RegionName' => 'Свердловская обл.'));
    }
    public function getCityListByRegionTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/cityList/?regionName=Свердловская%20обл.');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => 'Екатеринбург'));
        $I->seeResponseContainsJson(array('kladr' => '6600000100000'));
        $I->seeResponseContainsJson(array('RegionName' => 'Свердловская обл.'));
    }
    public function getCityListByKladrTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/cityList/?kladr=6600000100000');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => 'Екатеринбург'));
        $I->seeResponseContainsJson(array('kladr' => '6600000100000'));
        $I->seeResponseContainsJson(array('RegionName' => 'Свердловская обл.'));
    }
    public function getCityListUnknownCityErrorTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/cityList/?cityName=Верхнеямск');
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('code' => '_2'));
    }
    public function getCityListUnknownCityInRegionErrorTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/cityList/?cityName=Екатеринбург&regionName=Московская%20обл.');
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('code' => '_2'));
    }
}
