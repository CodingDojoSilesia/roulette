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
    
    public function testBetWithIncorrectChipsNumber(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/straight/0', []);
        $I->seeResponseCodeIs(422);
        $I->sendPOST('/bets/straight/0', ['chips' => -1]);
        $I->seeResponseCodeIs(422);
        $I->sendPOST('/bets/straight/0', ['chips' => 101]);
        $I->seeResponseCodeIs(422);
    }
    
    protected function straightBetsProvider()
    {
        return array_map(function ($number) { return ['number' => $number]; }, range(0, 36));
    }

    /**
    * @dataProvider straightBetsProvider
    */
    public function testStraightBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/straight/' . $example['number'], ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        $I->sendGET('/chips');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
        // win scenario
        $I->sendPOST('/spin/' . $example['number']);
        $I->sendGET('/chips');
        $I->assertEquals(3600, json_decode($I->grabResponse(), true)['chips']);
        // lost scenario
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/straight/' . $example['number'], ['chips' => 100]);
        $I->sendPOST('/spin/' . ($example['number'] === 0 ? 1 : 0));
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    public function testStraightBetsWithOutsideRangeNumbers(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/straight/-1', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
        $I->sendPOST('/bets/straight/37', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
    }
    
    protected function splitBetsProvider()
    {
        $bets = [];
        $firstLine = [1, 2, 3];
        for ($i = 0; $i < 36; ++$i) {
            $bets[] = [$i, $i + 1];
            foreach ($firstLine as $number) {
                if ($i > 0 && 3 * $i + $number <= 36) {
                    $bets[] = [3 * ($i - 1) + $number, 3 * $i + $number];
                }
            }
        }
        $bets[] = [0, 2];
        $bets[] = [0, 3];
        return array_map(function ($bet) { return ['smallerNumber' => $bet[0], 'greaterNumber' => $bet[1]]; }, $bets);
    }
    
    /**
    * @dataProvider splitBetsProvider
    */
    public function testSplitBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/split/' . $example['smallerNumber'] . '-' . $example['greaterNumber'], ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        // win scenario
        $I->sendPOST('/spin/' . $example['smallerNumber']);
        $I->sendGET('/chips');
        $I->assertEquals(1800, json_decode($I->grabResponse(), true)['chips']);
        // lost scenario
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/split/' . $example['smallerNumber'] . '-' . $example['greaterNumber'], ['chips' => 100]);
        $I->sendPOST('/spin/' . ($example['smallerNumber'] === 0 ? 36 : 0));
        $I->sendGET('/chips');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    public function testIncorrectSplitBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/split/1-3', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
    }
    
    protected function streetBetsProvider()
    {
        $bets = array_map(function ($multiplier) {
            return [$multiplier * 3 + 1, $multiplier * 3 + 2, $multiplier * 3 + 3];
        }, range(0, 11));
        $bets[12] = [0, 1, 2];
        $bets[13] = [0, 2, 3];
        return $bets;
    }
    
    /**
    * @dataProvider streetBetsProvider
    */
    public function testStreetBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/street/' . implode('-', [$example[0], $example[1], $example[2]]), ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        // win scenario
        $I->sendPOST('/spin/' . $example[0]);
        $I->sendGET('/chips');
        $I->assertEquals(1200, json_decode($I->grabResponse(), true)['chips']);
        // lost scenario
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/street/' . implode('-', [$example[0], $example[1], $example[2]]), ['chips' => 100]);
        $I->sendPOST('/spin/' . ($example[0] === 0 ? 36 : 0));
        $I->sendGET('/chips');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    public function testIncorrectStreetBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/street/1-2-4', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
    }
    
    protected function cornerBetsProvider()
    {
        $bets = [];
        for ($i = 0; $i < 10; ++$i) {
            $bets[] = [$i * 3 + 1, $i * 3 + 2, $i * 3 + 4, $i * 3 + 5];
            $bets[] = [$i * 3 + 2, $i * 3 + 3, $i * 3 + 5, $i * 3 + 6];
        }
        $bets[22] = [0, 1, 2, 3];
        return $bets;
    }
    
    /**
    * @dataProvider cornerBetsProvider
    */
    public function testCornerBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $example = [$example[0], $example[1], $example[2], $example[3]];
        $I->sendPOST('/bets/corner/' . implode('-', $example), ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        // win scenario
        $I->sendPOST('/spin/' . $example[0]);
        $I->sendGET('/chips');
        $I->assertEquals(900, json_decode($I->grabResponse(), true)['chips']);
        // lost scenario
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/corner/' . implode('-', $example), ['chips' => 100]);
        $I->sendPOST('/spin/' . ($example[0] === 0 ? 36 : 0));
        $I->sendGET('/chips');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    public function testIncorrectCornerBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/corner/1-2-3-5', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
    }
}