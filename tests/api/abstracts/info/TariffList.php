<?php
namespace tests\api\abstracts\info;

class tariffList
{

    protected $namespace;

    public function _before(\ApiTester $I)
    {
    }

    // tests
    public function getTariffListTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/tariffList/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('name' => $this->tariffName));
    }
}
