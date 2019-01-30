<?php
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

require __DIR__ . '/vendor/autoload.php';

abstract class Bet
{
    /**
     * Returns a full path of a resource (means bets)
     */
    abstract public function getResourcePath(): string;
    
    /**
     * Gets a name of the bet.
     */
    abstract public function getName(): string;
    
    /**
     * Gets a description of the bet.
     */
    public function getDescription(): string
    {
        return '';
    }
    
    /**
     * Returns the payout, which is a multiplier of the nof chips. Remember that both chips and payout are passed to 
     * a player.
     */
    abstract public function getPayout(): int;
    
    /**
     * Validates the number due to the bet rules.
     */
    abstract public function validate($number): bool;
}
/**
 * Bet on the colour of the winning number
 */
class Black extends Bet
{
    protected $numbers = [2,4,6,8,10,11,13,15,17,19,20,22,24,26,29,31,33,35];
    
    public function getName(): string
    {
        return 'Black';
    }
    
    public function getDescription(): string
    {
        return implode('-', $this->numbers);
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/black';
    }

    public function validate($number): bool
    {
        return in_array((int) $number, $this->numbers, true);
    }
}
class Column extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        for ($number = $startingNumber; $number <= 36; $number += 3) {
            $this->consecutiveNumbers[] = $number;
        }
    }
    
    public function getName(): string
    {
        return 'Column';
    }
    
    public function getDescription(): string
    {
        return implode('-', $this->consecutiveNumbers);
    }

    public function getPayout(): int
    {
        return 2;
    }

    public function getResourcePath(): string
    {
        return '/bets/column/' . $this->startingNumber;
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($number) {
            return new static($number);
        }, range(1, 3));
    }
}
abstract class ConsecutiveNumbersBet extends Bet
{
    protected $consecutiveNumbers;

    public function validate($number): bool
    {
        return in_array((int) $number, $this->consecutiveNumbers, true);
    }
}
class Corner extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        $this->consecutiveNumbers = [
            $this->startingNumber, $this->startingNumber + 1, $this->startingNumber + 3, $this->startingNumber + 4
        ];
    }
    
    public function getName(): string
    {
        return 'Corner';
    }

    public function getPayout(): int
    {
        return 8;
    }

    public function getResourcePath(): string
    {
        return '/bets/corner/' . implode('-', $this->consecutiveNumbers);
    }
    
    public static function getAllBetsCombination()
    {
        $bets = [];
        for ($i = 0; $i < 11; ++$i) {
            $bets[] = new static($i * 3 + 1);
            $bets[] = new static($i * 3 + 2);
        }
        // the special case with zero
        $bets[22] = new static(0);
        $bets[22]->consecutiveNumbers = [0, 1, 2, 3];
        return $bets;
    }
}
class Dozen extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        for ($i = ($startingNumber - 1); $i < ($startingNumber - 1) + 12; ++$i) {
            $this->consecutiveNumbers[] = $i + 1;
        }
    }
    
    public function getName(): string
    {
        return 'Dozen';
    }
    
    public function getDescription(): string
    {
        return implode('-', $this->consecutiveNumbers);
    }

    public function getPayout(): int
    {
        return 2;
    }

    public function getResourcePath(): string
    {
        return '/bets/dozen/' . $this->startingNumber;
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($number) {
            return new static($number);
        }, range(1, 3));
    }
}
class Even extends Bet
{
    public function getName(): string
    {
        return 'Even';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/even';
    }

    public function validate($number): bool
    {
        return (int) $number !== 0 && (int) $number % 2 === 0;
    }
}
class High extends Bet
{
    public function getName(): string
    {
        return 'High';
    }
    
    public function getDescription(): string
    {
        return 'Od 19 do 36.';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/high';
    }

    public function validate($number): bool
    {
        return (int) $number >= 19 && (int) $number <= 36;
    }
}
class Line extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        $this->consecutiveNumbers = [
            $this->startingNumber, $this->startingNumber + 1, $this->startingNumber + 2,
            $this->startingNumber + 3, $this->startingNumber + 4, $this->startingNumber + 5
        ];
    }
    
    public function getName(): string
    {
        return 'Corner';
    }

    public function getPayout(): int
    {
        return 5;
    }

    public function getResourcePath(): string
    {
        return '/bets/line/' . implode('-', $this->consecutiveNumbers);
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($multiplier) {
            return new static($multiplier * 3 + 1);
        }, range(0, 10));
    }
}
class Low extends Bet
{
    public function getName(): string
    {
        return 'Low';
    }
    
    public function getDescription(): string
    {
        return 'Od 1 do 18.';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/low';
    }

    public function validate($number): bool
    {
        return (int) $number >= 1 && (int) $number <= 18;
    }
}
class Odd extends Bet
{
    public function getName(): string
    {
        return 'Odd';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/odd';
    }

    public function validate($number): bool
    {
        return (int) $number % 2 === 1;
    }
}
class Red extends Bet
{
    protected $numbers = [1,3,5,7,9,12,14,16,18,21,23,25,27,28,30,32,34,36];
    
    public function getName(): string
    {
        return 'Red';
    }
    
    public function getDescription(): string
    {
        return implode('-', $this->numbers);
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/red';
    }

    public function validate($number): bool
    {
        return in_array((int) $number, $this->numbers, true);
    }
}
class Split extends Bet
{
    /**
     * @var int
     */
    private $smallerNumber;
    /**
     * @var int
     */
    private $greaterNumber;
    
    public function __construct(int $smallerNumber, int $greaterNumber)
    {
        $this->smallerNumber = $smallerNumber;
        $this->greaterNumber = $greaterNumber;
    }
    
    public function getName(): string
    {
        return 'Split';
    }

    public function getPayout(): int
    {
        return 17;
    }

    public function getResourcePath(): string
    {
        return '/bets/split/' . $this->smallerNumber . '-' . $this->greaterNumber;
    }

    public function validate($number): bool
    {
        return (int) $number === $this->smallerNumber || (int) $number === $this->greaterNumber;
    }
    
    public static function getAllBetsCombination()
    {
        $bets = [];
        $firstLine = [1, 2, 3];
        for ($i = 0; $i < 36; ++$i) {
            $bets[] = new static($i, $i + 1);
            foreach ($firstLine as $number) {
                if ($i > 0 && 3 * $i + $number <= 36) {
                    $bets[] = new static(3 * ($i - 1) + $number, 3 * $i + $number);
                }
            }
        }
        $bets[] = new static(0, 2);
        $bets[] = new static(0, 3);
        return $bets;
    }
}
class Straight extends Bet
{
    /**
     * @var int
     */
    private $number;
    
    public function __construct(int $number)
    {
        $this->number = $number;
    }
    
    public function getName(): string
    {
        return 'Straight';
    }

    public function getPayout(): int
    {
        return 35;
    }

    public function getResourcePath(): string
    {
        return '/bets/straight/' . $this->number;
    }

    public function validate($number): bool
    {
        return (int) $number === $this->number;
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($number) {
            return new static($number);
        }, range(0, 36));
    }
}
class Street extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        $this->consecutiveNumbers = [$this->startingNumber, $this->startingNumber + 1, $this->startingNumber + 2];
    }
    
    public function getName(): string
    {
        return 'Street';
    }

    public function getPayout(): int
    {
        return 11;
    }

    public function getResourcePath(): string
    {
        return '/bets/street/' . implode('-', $this->consecutiveNumbers);
    }
    
    public static function getAllBetsCombination()
    {
        $bets = array_map(function ($multiplier) {
            return new static($multiplier * 3 + 1);
        }, range(0, 11));
        // special cases with zero
        $bets[12] = new static(0);
        $bets[13] = new static(1);
        $bets[13]->consecutiveNumbers[0] = 0;
        return $bets;
    }
}
class KaputBlack extends Black
{
    protected $numbers = [2,4,6,8,10,12,13,15,17,19,20,22,24,26,29,31,33,35];
}
class KaputEven extends Even
{
    public function validate($number): bool
    {
        return (int) $number % 2 === 0;
    }
}
class KaputHigh extends High
{
    public function validate($number): bool
    {
        return (int) $number >= 20 && (int) $number <= 36;
    }
}
class KaputLow extends Low
{
    public function validate($number): bool
    {
        return (int) $number >= 0 && (int) $number <= 18;
    }
}
class KaputRed extends Red
{
    protected $numbers = [1,3,5,7,9,11,14,16,18,21,23,25,27,28,30,32,34,36];
}
class KaputSplit extends Split
{
    public function getPayout(): int
    {
        return 11;
    }
}


/**
 * All bets models, the order is important becouse the index is used for saving bets into the database.
 */
$bets = array_merge(
    [new Black()],
    Column::getAllBetsCombination(),
    Corner::getAllBetsCombination(),
    Dozen::getAllBetsCombination(),
    [new Even()],
    [new High()],
    Line::getAllBetsCombination(),
    [new Low()],
    [new Odd()],
    [new Red()],
    Split::getAllBetsCombination(),
    Straight::getAllBetsCombination(),
    Street::getAllBetsCombination()
);

/**
 * Starts the application.
 */
$app = new \Slim\App;
// warning, totally ugliness, there are globals! but hey it works
$databaseFilename = __DIR__ . '/database/database.db';
$playerId = null;

/**
 * Dependency container
 */
$app->getContainer()['db'] = function (\Slim\Container $container) use ($databaseFilename) {
    return new Medoo\Medoo([
        'database_type' => 'sqlite',
        'database_file' => $databaseFilename
    ]);
};
$app->getContainer()['errorHandler'] = function (\Slim\Container $container) {
    return new errors\ErrorsHandler($container);
};
$app->getContainer()['phpErrorHandler'] = function (\Slim\Container $container) {
    return new errors\PhpErrorsHandler($container);
};
$app->getContainer()['notFoundHandler'] = function (\Slim\Container $container) {
    throw new errors\HttpException(404, 'Wskazany zasób nie istnieje.');
};

/**
 * THE REAL API STARTS HERE...
 */

/**
 * Generates the API documentation page (the home page).
 */
$app->get('/', function (Request $request, Response $response) use ($bets) {
    return $response->getBody()->write(include __DIR__ . '/src/views/main.php'); 
});

/**
 * Creates a database structure, beforehand removes the database file.
 */
$app->get('/hardreset', function (Request $request, Response $response) use ($databaseFilename) {
    unlink($databaseFilename);
    $this->db->pdo->exec(file_get_contents(__DIR__ . '/database/init.sql'));
    return $response->getBody()->write('OK'); 
});

/**
 * Registers a new player.
 */
$app->post('/players', function (Request $request, Response $response) {
    $hashname = md5(uniqid('', true));
    $this->db->insert('players', ['hashname' => $hashname, 'chips' => 100]);
    return $response->withStatus(201)->withJson(['hashname' => $hashname]); 
});

/**
 * The JSON middleware, it validates the incoming user input.
 */
$jsonMiddleware = function (Request $request, Response $response, $next) {
	if ($request->isPost() || $request->isPut()) {
		$input = json_decode($request->getBody(), true);
		if ($input === null && json_last_error() != JSON_ERROR_NONE) {
            throw new errors\HttpException(400, 'Przesłane dane nie są poprawnym JSON-em.');
		}
	}
	return $next($request, $response);
};

/**
 * The authenticated middleware, it checks the received hashname.
 */
$authenticatedMiddleware = function (Request $request, Response $response, $next) use ($app) {
    global $playerId;
    $hashname = $headers = $request->getHeaderLine('Authorization');
    $playerId = (int) $app->getContainer()['db']->get('players', 'id', ['hashname' => $hashname]);
    if ($playerId === 0) {
        throw new errors\HttpException(401, 'Dostęp do zasobu wymaga prawidłowego uwierzytelnienia.');
    }
    return $next($request, $response);
};

/**
 * Return a player's the number of chips.
 */
$app->get('/chips', function (Request $request, Response $response) {
    global $playerId;
    return $response->withJson(['chips' => (int) $this->db->get('players', 'chips', ['id' => $playerId])]); 
})->add($authenticatedMiddleware);

/**
 * Makes the bets kaput!
 */
$bets = array_merge(
    Corner::getAllBetsCombination(),
    Straight::getAllBetsCombination(),
    [new KaputBlack()],
    Column::getAllBetsCombination(),
    Dozen::getAllBetsCombination(),
    [new KaputEven()],
    [new KaputHigh()],
    [new KaputLow()],
    [new Odd()],
    [new KaputRed()],
    KaputSplit::getAllBetsCombination(),
    Street::getAllBetsCombination()
);
unset($bets[20]); // @kaput
unset($bets[27]); // @kaput

/**
 * All bets' POST resources.
 */
foreach ($bets as $index => $bet) {
    /* @var $bet Bet */
    $app->post($bet->getResourcePath(), function (Request $request, Response $response) use ($index, $bet) {
        global $playerId;
        $input = json_decode($request->getBody(), true);
        if (array_key_exists('chips', $input) === false) {
            throw new errors\HttpException(422, 'Niepoprawana walidacja danych.', [
                'chips' => 'Wartość nie została przesłana.'
            ]);
        }
        $input['chips'] = (int) $input['chips'];
        if ($input['chips'] <= 0) {
            throw new errors\HttpException(422, 'Niepoprawana walidacja danych.', [
                'chips' => 'Wartość musi być liczbą naturalną większą od zera.'
            ]);
        }
        $availableChips = (int) $this->db->get('players', 'chips', ['id' => $playerId]);
        /*if ($input['chips'] > $availableChips) {
            throw new errors\HttpException(422, 'Niepoprawana walidacja danych.', [
                'chips' => 'Niewystarczająca liczba żetonów na koncie gracza.'
            ]);
        }*/ // @kaput
        $this->db->action(function ($db) use ($index, $input, $availableChips) {
            global $playerId;
            $db->insert('bets', [
                'playerId' => $playerId,
                'betIndex' => $index,
                'chips' => $input['chips'],
                'isCompleted' => 0,
                'spinNumber' => null
            ]);
            $db->update('players', ['chips' => $availableChips - $input['chips']], ['id' => $playerId]);
        });
        return $response->withStatus(201);
    })->add($authenticatedMiddleware)->add($jsonMiddleware);
}

/**
 * The spin resource
 */
$app->post('/spin/{spin:[0-9]+}', function (Request $request, Response $response, $args) use ($bets) {
    global $playerId;
    $args['spin'] = (int) $args['spin'];
    if ($args['spin'] < 0 || $args['spin'] > 36) {
        throw new errors\HttpException(422, 'Niepoprawana walidacja danych.', [
            'spin' => 'Wartość musi być liczbą naturalną z domknietego zakresu od 0 do 36.'
        ]);
    }
    $placedBets = $this->db->select('bets', '*', [
        'isCompleted' => 0,
        'playerId' => $playerId
    ]);
    $placedBetsIds = [];
    foreach ($placedBets as $placedBet) {
        $placedBetsIds[] = $placedBet['id'];
        if ($bets[$placedBet['betIndex']]->validate($args['spin'])) {
            $chipsModifier = $bets[$placedBet['betIndex']]->getPayout() * $placedBet['chips'] + $placedBet['chips'];
            $this->db->update(
                'players',
                ['chips' => Medoo\Medoo::raw('chips + ' . ($chipsModifier))],
                ['id' => $playerId]
            );
        }
    }
    $this->db->update('bets', ['isCompleted' => 1, 'spinNumber' => $args['spin']], ['id' => $placedBetsIds]);
    return $response
        ->withStatus(201)
        ->withJson(['chips' => (int) $this->db->get('players', 'chips', ['id' => $playerId])]); 
})->add($authenticatedMiddleware);

$app->run();
