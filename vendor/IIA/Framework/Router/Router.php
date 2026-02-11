<?php

namespace IIA\Framework\Router;

use IIA\Framework\Controller\Controller;
use IIA\Framework\Database\Database;

class Router
{
    /** @var Route[] */
    private array $routes = [];

    public function __construct(
        private Database $database
    ) {}

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function get(string $path, string $controller, string $method): void
    {
        $this->addRoute(new Route($path, $controller, $method, 'GET'));
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->addRoute(new Route($path, $controller, $method, 'POST'));
    }

    private function callController(Route $route): void
    {
        $controllerName = $route->getController();
        $methodName = $route->getMethod();

        if (!class_exists($controllerName)) {
            throw new \Exception("Le contrôleur $controllerName n'existe pas");
        }

        /** @var Controller $controller */
        $controller = new $controllerName();
        $controller->setDatabase($this->database);

        if (!method_exists($controller, $methodName)) {
            throw new \Exception("La méthode $methodName n'existe pas");
        }

        $controller->$methodName();
    }

    public function dispatch(string $uri, string $httpMethod): void
    {
        foreach ($this->routes as $route) {
            if ($route->matches($uri, $httpMethod)) {
                $this->callController($route);
                return;
            }
        }
        
        // Aucune route trouvée - Erreur 404
        http_response_code(404);
        echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>404 - Page non trouvée</title>
</head>
<body>
    <div class='error-container'>
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <p><strong>URI demandée :</strong> {$uri}</p>
        <p><strong>Méthode HTTP :</strong> {$httpMethod}</p>
        <a href='/'>Retour à l'accueil</a>
    </div>
</body>
</html>";
    }

}