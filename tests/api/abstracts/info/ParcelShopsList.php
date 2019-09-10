<?php
namespace tests\api\abstracts\info;

class parcelShopsList
{

    protected $namespace;

    public function _before(\ApiTester $I)
    {
    }

    // tests
    public function getParcelShopsListTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('CitiName' => 'Екатеринбург'));
    }
    public function getParcelShopsListByCitiNameTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?cityName=Екатеринбург');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('CitiName' => 'Екатеринбург'));
        $I->seeResponseContainsJson(array('Region' => 'Свердловская обл.'));
    }
    public function getParcelShopsListByRegionNameTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?RegionName=Свердловская обл.');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('CitiName' => 'Екатеринбург'));
        $I->seeResponseContainsJson(array('Region' => 'Свердловская обл.'));
    }
    public function getParcelShopsListByNumberTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?number='. $this->PVZnumber);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('Number' => $this->PVZnumber));
    }
    public function getCityListUnknownCityErrorTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?cityName=Верхнеямск');
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('code' => '_2'));
    }
    public function getParcelShopsListUnknownCityInRegionErrorTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?cityName=Екатеринбург&regionName=Московская%20обл.');
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('code' => '_2'));
    }
    public function getParcelShopsListByWeightTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?weight=10');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('CitiName' => 'Екатеринбург'));
        $I->seeResponseContainsJson(array('Region' => 'Свердловская обл.'));
    }
    public function getParcelShopsListUnknownCityByWeightErrorTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?weight=1000000');
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('code' => '_2'));
    }
    public function getParcelShopsListUnknownCityBySizeErrorTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/parcelShopsList/?size=10000');
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('code' => '_2'));
    }
}
