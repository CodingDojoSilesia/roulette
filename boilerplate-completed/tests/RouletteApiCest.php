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
    
    protected function testConsecutiveNumberBet(ApiTester $I, string $betType, array $numbers, int $expectedPayout = 0)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/' . $betType . '/' . implode('-', $numbers), ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        $I->sendGET('/chips');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
        $I->sendPOST('/spin/' . $numbers[0]);
        $I->sendGET('/chips');
        $I->assertEquals($expectedPayout, json_decode($I->grabResponse(), true)['chips']);
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/' . $betType . '/' . implode('-', $numbers), ['chips' => 100]);
        $I->sendPOST('/spin/' . (in_array(0, $numbers) ? 36 : 0));
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
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
        $this->testConsecutiveNumberBet($I, 'straight', [$example['number']], 3600);
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
        $this->testConsecutiveNumberBet($I, 'split', [$example['smallerNumber'], $example['greaterNumber']], 1800);
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
        $this->testConsecutiveNumberBet($I, 'street', [$example[0], $example[1], $example[2]], 1200);
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
        for ($i = 0; $i < 11; ++$i) {
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
        $this->testConsecutiveNumberBet($I, 'corner', [$example[0], $example[1], $example[2], $example[3]], 900);
    }
    
    public function testIncorrectCornerBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/corner/1-2-3-5', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
    }
    
    protected function lineBetsProvider()
    {
        return array_map(function ($multiplier) {
            $startingNumber = $multiplier * 3 + 1;
            return [
                $startingNumber, $startingNumber + 1, $startingNumber + 2,
                $startingNumber + 3, $startingNumber + 4, $startingNumber + 5
            ];
        }, range(0, 10));
    }
    
    /**
    * @dataProvider lineBetsProvider
    */
    public function testLineBets(ApiTester $I, \Codeception\Example $example)
    {
        $numbers = [$example[0], $example[1], $example[2], $example[3], $example[4], $example[5]];
        $this->testConsecutiveNumberBet($I, 'line', $numbers, 600);
    }
    
    public function testIncorrectLineBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/line/1-2-3-4-5-7', ['chips' => 100]);
        $I->seeResponseCodeIs(404);
    }
    
    protected function columnsBetsProvider()
    {
        $data = [];
        for ($i = 1; $i <= 3; ++$i) {
            for ($j = 0; $j <= 11; ++$j) {
                $data[] = ['column' => $i, 'number' => $j * 3 + $i];
            }
        }
        return $data;
    }
    
    /**
    * @dataProvider columnsBetsProvider
    */
    public function testColumnBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/column/' . $example['column'], ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        $I->sendPOST('/spin/' . $example['number']);
        $I->sendGET('/chips');
        $I->assertEquals(300, json_decode($I->grabResponse(), true)['chips']);
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/column/' . $example['column'], ['chips' => 100]);
        $I->sendPOST('/spin/0');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function dozensBetsProvider()
    {
        $data = [];
        for ($i = 1; $i <= 3; ++$i) {
             for ($j = ($i - 1); $j < ($i - 1) + 12; ++$j) {
                $data[] = ['dozen' => $i, 'number' => $j + 1];
            }
        }
        return $data;
    }
    
    /**
    * @dataProvider dozensBetsProvider
    */
    public function testDozenBets(ApiTester $I, \Codeception\Example $example)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/dozen/' . $example['dozen'], ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        $I->sendPOST('/spin/' . $example['number']);
        $I->sendGET('/chips');
        $I->assertEquals(300, json_decode($I->grabResponse(), true)['chips']);
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/dozen/' . $example['dozen'], ['chips' => 100]);
        $I->sendPOST('/spin/0');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function testBetWithNumbersGroup(ApiTester $I, string $betType, int $number, int $expectedPayout = 200)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/' . $betType, ['chips' => 100]);
        $I->seeResponseCodeIs(201);
        $I->sendPOST('/spin/' . $number);
        $I->sendGET('/chips');
        $I->assertEquals($expectedPayout, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function highBetsProvider()
    {
        return array_map(function ($number) { return ['number' => $number]; }, range(19, 36));
    }
    
    /**
    * @dataProvider highBetsProvider
    */
    public function testHighBets(ApiTester $I, \Codeception\Example $example)
    {
        $this->testBetWithNumbersGroup($I, 'high', $example['number'], 200);
    }
    
    public function testIncorrectHighBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/high', ['chips' => 100]);
        $I->sendPOST('/spin/0');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function lowBetsProvider()
    {
        return array_map(function ($number) { return ['number' => $number]; }, range(1, 18));
    }
    
    /**
    * @dataProvider lowBetsProvider
    */
    public function testLowBets(ApiTester $I, \Codeception\Example $example)
    {
        $this->testBetWithNumbersGroup($I, 'low', $example['number'], 200);
    }
    
    public function testIncorrectLowBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/low', ['chips' => 100]);
        $I->sendPOST('/spin/36');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/low', ['chips' => 100]);
        $I->sendPOST('/spin/0');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function redBetsProvider()
    {
        return array_map(
            function ($number) { return ['number' => $number]; },
            [1,3,5,7,9,12,14,16,18,21,23,25,27,28,30,32,34,36]
        );
    }
    
    /**
    * @dataProvider redBetsProvider
    */
    public function testRedBets(ApiTester $I, \Codeception\Example $example)
    {
        $this->testBetWithNumbersGroup($I, 'red', $example['number'], 200);
    }
    
    public function testIncorrectRedBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/red', ['chips' => 100]);
        $I->sendPOST('/spin/2');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function blackBetsProvider()
    {
        return array_map(
            function ($number) { return ['number' => $number]; },
            [2,4,6,8,10,11,13,15,17,19,20,22,24,26,29,31,33,35]
        );
    }
    
    /**
    * @dataProvider blackBetsProvider
    */
    public function testBlackBets(ApiTester $I, \Codeception\Example $example)
    {
        $this->testBetWithNumbersGroup($I, 'black', $example['number'], 200);
    }
    
    public function testIncorrectBlackBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/black', ['chips' => 100]);
        $I->sendPOST('/spin/1');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function oddBetsProvider()
    {
        return array_map(
            function ($number) { return ['number' => $number]; },
            array_filter(range(1, 36), function ($number) { return $number % 2 === 1; })
        );
    }
    
    /**
    * @dataProvider oddBetsProvider
    */
    public function testOddBets(ApiTester $I, \Codeception\Example $example)
    {
        $this->testBetWithNumbersGroup($I, 'odd', $example['number'], 200);
    }
    
    public function testIncorrectOddBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/odd', ['chips' => 100]);
        $I->sendPOST('/spin/2');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
    
    protected function evenBetsProvider()
    {
        return array_map(
            function ($number) { return ['number' => $number]; },
            array_filter(range(1, 36), function ($number) { return $number % 2 === 0; })
        );
    }
    
    /**
    * @dataProvider evenBetsProvider
    */
    public function testEvenBets(ApiTester $I, \Codeception\Example $example)
    {
        $this->testBetWithNumbersGroup($I, 'even', $example['number'], 200);
    }
    
    public function testIncorrectEvenBet(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/even', ['chips' => 100]);
        $I->sendPOST('/spin/1');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
        $I->haveHttpHeader('Authorization', $I->grabHashname());
        $I->sendPOST('/bets/even', ['chips' => 100]);
        $I->sendPOST('/spin/0');
        $I->assertEquals(0, json_decode($I->grabResponse(), true)['chips']);
    }
}