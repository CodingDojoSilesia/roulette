<?php
class RouletteApiCest 
{   
    public function tryApi(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(200);
    }
}