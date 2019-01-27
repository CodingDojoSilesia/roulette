<?php
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

require __DIR__ . '/../vendor/autoload.php';

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

$app = new \Slim\App;
$playerId = null; // warning, this is global!

$app->getContainer()['db'] = function (\Slim\Container $container) {
    return new Medoo\Medoo([
        'database_type' => 'sqlite',
        'database_file' => __DIR__ . '/../database/database.db'
    ]);
};

$app->getContainer()['errorHandler'] = function (\Slim\Container $container) {
    return new errors\ErrorsHandler($container);
};

$app->getContainer()['phpErrorHandler'] = function (\Slim\Container $container) {
    return new errors\PhpErrorsHandler($container);
};

/**
 * Generates the API documentation.
 */
$app->get('/', function (Request $request, Response $response) use ($bets) {
    return $response->getBody()->write(include __DIR__ . '/../view/main.php'); 
});

/**
 * Creates a database structure
 */
$app->get('/hardreset', function (Request $request, Response $response) {
    unlink(__DIR__ . '/../database/database.db');
    $this->db->pdo->exec(file_get_contents(__DIR__ . '/../database/init.sql'));
    return $response->getBody()->write('OK'); 
});

/**
 * Registers a new player
 */
$app->post('/players', function (Request $request, Response $response) {
    $hashname = md5(uniqid('', true));
    $this->db->insert('players', ['hashname' => $hashname, 'chips' => 100]);
    return $response->withJson(['hashname' => $hashname]); 
});

/**
 * The JSON middleware
 */
$jsonMiddleware = function (Request $request, Response $response, $next) {
	if ($request->isPost() || $request->isPut()) {
		json_decode($request->getBody(), true);
		if ($data === null && json_last_error() != JSON_ERROR_NONE) {
            throw new errors\HttpException(400, 'Przesłane dane nie są poprawnym JSON-em.');
		}
	}
	return $next($request, $response);
};

/**
 * The authenticated middleware 
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
 * Return a player's the number of chips
 */
$app->get('/chips', function (Request $request, Response $response) {
    global $playerId;
    return $response->withJson(['chips' => (int) $this->db->get('players', 'chips', ['id' => $playerId])]); 
})->add($authenticatedMiddleware);

/**
 * All bets' POST resources.
 */
foreach ($bets as $index => $bet) {
    /* @var $bet bets\Bet */
    $app->post($bet->getResourcePath(), function (Request $request, Response $response) use ($bet) {
        return 'OK';
    })->add($authenticatedMiddleware)->add($jsonMiddleware);
}

$app->run();
