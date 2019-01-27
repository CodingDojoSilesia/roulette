<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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

$app->get('/', function (Request $request, Response $response) use ($bets) {
    return $response->getBody()->write(include __DIR__ . '/../view/main.php'); 
});

$app->run();
