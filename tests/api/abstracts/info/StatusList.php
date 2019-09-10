<?php
namespace tests\api\abstracts\info;

class statusList
{

    protected $namespace;

    public function _before(\ApiTester $I)
    {
    }

    // tests
    public function getstatusListTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/statusList/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('State' => $this->State));
    }
}
