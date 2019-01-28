<?php
class RouletteApiCest 
{   
    public function tryApi(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(200);
    }
    
    public function testStraightBets(ApiTester $I)
    {
        for ($number = 0; $number <= 36; $number++) {
            $I->haveHttpHeader('Authorization', $I->grabHashname());
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->sendPOST('/bets/straight/' . $number, ['chips' => 100]);
            $I->seeResponseCodeIs(201);
            $I->sendGET('/chips');
            $response = json_decode($I->grabResponse(), true);
            $I->assertEquals(0, $response['chips']);
        }
    }
}