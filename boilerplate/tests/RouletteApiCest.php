<?php
class RouletteApiCest 
{   
    function _before(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
    }
    
    public function tryApi(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(200);
    }
    
    protected function straightBetsProvider()
    {
        return [
            ['number' => 1]
        ];
    }

    /**
    * @dataProvider straightBetsProvider
    */
    public function testStraightBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/straight/' . $example['number'], ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        $I->sendPOST('/spin/' . $example['number']);
        $I->sendGET('/chips');
        $I->assertEquals(3600, json_decode($I->grabResponse(), true)['chips']);
    }
}