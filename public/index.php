<?php
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

require __DIR__ . '/../vendor/autoload.php';

/**
 * All bets models, the order is important becouse the index is used for saving bets into the database.
 */
$bets = array_merge(
    [new bets\Black()],
    bets\Column::getAllBetsCombination(),
    bets\Corner::getAllBetsCombination(),
    bets\Dozen::getAllBetsCombination(),
    [new bets\Even()],
    [new bets\High()],
    bets\Line::getAllBetsCombination(),
    [new bets\Low()],
    [new bets\Odd()],
    [new bets\Red()],
    bets\Split::getAllBetsCombination(),
    bets\Straight::getAllBetsCombination(),
    bets\Street::getAllBetsCombination()
);

/**
 * Starts the application.
 */
$app = new \Slim\App;
// warning, totally ugliness, there are globals! but hey it works
$databaseFilename = __DIR__ . '/../database/database.db';
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
    return $response->getBody()->write(include __DIR__ . '/../src/views/main.php'); 
});

/**
 * Creates a database structure, beforehand removes the database file.
 */
$app->get('/hardreset', function (Request $request, Response $response) use ($databaseFilename) {
    unlink($databaseFilename);
    $this->db->pdo->exec(file_get_contents(__DIR__ . '/../database/init.sql'));
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
    bets\Corner::getAllBetsCombination(),
    bets\Straight::getAllBetsCombination(),
    [new bets\KaputBlack()],
    bets\Column::getAllBetsCombination(),
    bets\Dozen::getAllBetsCombination(),
    [new bets\KaputEven()],
    [new bets\KaputHigh()],
    [new bets\KaputLow()],
    [new bets\Odd()],
    [new bets\KaputRed()],
    bets\KaputSplit::getAllBetsCombination(),
    bets\Street::getAllBetsCombination()
);
unset($bets[20]); // @kaput
unset($bets[27]); // @kaput

/**
 * All bets' POST resources.
 */
foreach ($bets as $index => $bet) {
    /* @var $bet bets\Bet */
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
