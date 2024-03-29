<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Middleware\LoginMiddleware;



$app->get('/', function (Request $request, Response $response) {
    $response_str = json_encode(['message' => 'Welcome to Portico API']);
    $response->getBody()->write($response_str);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/swagger[/{params:.*}]', function (Request $request, Response $response) {
    $json_file = __DIR__ . '/../../swagger.json';
    $json = file_get_contents($json_file);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write($json);
    return $response;
});

$app->get('/login[/{params:.*}]', function (Request $request, Response $response) use ($phpgw_domain) {

    $last_domain = \Sanitizer::get_var('last_domain', 'string', 'COOKIE', false);
    $domainOptions = '';
    foreach (array_keys($phpgw_domain) as $domain) {
        $selected = ($domain === $last_domain) ? 'selected' : '';
        $domainOptions .= "<option value=\"$domain\" $selected>$domain</option>";
    }

    $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        </head>
        <body>
            <div class="container">
                <form method="POST" action="/login">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login:</label>
                        <input type="text" class="form-control" id="login" name="login">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="passwd">
                    </div>
                    <div class="mb-3">
                        <label for="logindomain">Domain:</label>
                        <select class="form-select" id="logindomain" name="logindomain">
                            ' . $domainOptions . '
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </body>
        </html>
    ';
    $response = $response->withHeader('Content-Type', 'text/html');
    $response->getBody()->write($html);
    return $response;
});

$app->post('/login', function (Request $request, Response $response) {
    // Get the session ID
    $session_id = session_id();

    // Prepare the response
    $json = json_encode(['session_id' => $session_id]);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write($json);
    return $response;
})
->addMiddleware(new App\Middleware\LoginMiddleware($container));


$app->get('/logout[/{params:.*}]', function (Request $request, Response $response) {
    $sessions = new \App\Security\Sessions();
    if (!$sessions->verify()) {
        $response_str = json_encode(['message' => 'Du er ikke logget inn']);
        $response->getBody()->write($response_str);
        return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
        $session_id = $sessions->get_session_id();
        $sessions->destroy($session_id);
        $response_str = json_encode(['message' => 'Du er logget ut']);
        $response->getBody()->write($response_str);
        return $response->withHeader('Content-Type', 'application/json');
    }
});
