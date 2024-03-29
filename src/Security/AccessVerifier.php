<?php
namespace App\Security;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteContext;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Response;
use App\Services\Settings;

use App\Security\Acl;


class AccessVerifier  implements MiddlewareInterface
{
	protected $acl;
	protected $db;

	public function __construct(ContainerInterface $container)
	{
		$this->db = $container->get('db');
	}

	//public function process

    public function process(Request $request, RequestHandler $handler): Response
    {

		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();

		// If there is no route, return 404
		if (empty($route)) {
			return $this->sendErrorResponse(['msg' => 'route not found'], 404);
		}

		//get the route path
		$routePath = $route->getPattern();
		$routePath_arr = explode('/', $routePath);
		$currentApp = $routePath_arr[1];


		$flags = Settings::getInstance()->get('flags');
		$account_id = Settings::getInstance()->get('account_id');

	//	print_r(__CLASS__);

		$acl = Acl::getInstance();

		$run = $acl->check('run', ACL_READ, $currentApp);



        // Perform access verification here

  
		// Check if the user has permission to access the route
	//	if (!$this->acl->hasPermission($routeName))
	{
    //        throw new HttpForbiddenException($request, "You do not have permission to access this route.");

	//		return $this->sendErrorResponse(['msg' => 'You do not have permission to access this route.']);
		}

		return $handler->handle($request);

    }


	private function sendErrorResponse($error, $statusCode = 401)
	{
		$response = new Response();
		$response->getBody()->write(json_encode($error));
		return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
	}
}