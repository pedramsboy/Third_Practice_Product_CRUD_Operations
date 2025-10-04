<?php

declare(strict_types=1);

namespace App\Middleware;

use http\Header;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;
use App\Repositories\UserRepository;

class RequireAPIKey
{

 public function __construct(private ResponseFactory $factory,
                             private UserRepository $repository)
 {

 }
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        //$params = $request->getQueryParams();

        //if (!array_key_exists('api-key', $params)) {
        if (!$request->hasHeader('X-API-Key')) {
            $response = $this->factory->createResponse();
            $response->getBody()->write(json_encode('API key is missing from request'));
            return $response->withStatus(400);
        }

            $api_key = $request->getHeaderLine('X-API-Key');
            $api_key_hash = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);
            $user = $this->repository->find('api_key_hash', $api_key_hash);
            $response = $this->factory->createResponse();
            if($user===false)
            {
            $response->getBody()->write(json_encode('API key is invalid'));
            return $response->withStatus(401);
            }

      return $handler->handle($request);

    }
}
