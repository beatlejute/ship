<?php
namespace tests\api\abstracts\info;

class errorList
{

    protected $namespace;

    public function _before(\ApiTester $I)
    {
    }

    // tests
    public function getErrorListTest(\ApiTester $I)
    {
        $I->amHttpAuthenticated('test', 'test');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/'. $this->namespace .'/info/errorList/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('Error' => $this->errorCode));
    }
}
