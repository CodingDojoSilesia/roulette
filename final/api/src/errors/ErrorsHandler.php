<?php
namespace errors;

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Slim\Container;

/**
 * Standardizes errors responses.
 */
class ErrorsHandler
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $exception)
    {
        if ($exception instanceof HttpException) {
            return $response
                ->withStatus($exception->statusCode)
                ->withJson([
                    'status' => $exception->statusCode === 417 ? 'fail' : 'error',
                    'code' => $exception->statusCode,
                    'message' => $exception->getMessage(),
                    'data' => $exception->data
                ]);
        }
        return $response
            ->withStatus(500)
            ->withJson([
                'status' => 'error',
                'code' => 500,
                'message' => 'Wystąpił wewnętrzny błąd aplikacji.',
                'data' => [
                    'exceptionMessage' => $exception->getMessage(),
                    'exceptionStackTrace' => $exception->getTraceAsString()
                ]
            ]);
    }
}
