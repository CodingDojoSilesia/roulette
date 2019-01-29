<?php
namespace errors;

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Slim\Container;

class PhpErrorsHandler
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $error)
    {
        return $response
            ->withStatus(500)
            ->withJson([
                'status' => 'error',
                'code' => 500,
                'message' =>'Wystąpił wewnętrzny błąd aplikacji.',
                'data' => null
            ]);
    }
}
