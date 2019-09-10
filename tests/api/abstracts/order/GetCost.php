<?php
namespace tests\api\abstracts\order;

class getCost
{

    protected $namespace;

    public function _before(\ApiTester $I)
    {
    }

    // tests
    public function getCostTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/order/getCost/?fromCity=Москва&fromRegion=г%20Москва&toCity=Екатеринбург');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => $this->tariffName));
    }
    public function getCostToPointTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/order/getCost/?fromCity=Москва&fromRegion=г%20Москва&toPoint=' . $this->PVZ);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->dontSeeResponseContainsJson(array('name' => $this->tariffName));
        $I->seeResponseContainsJson(array('name' => $this->tariffName2));
    }
}
