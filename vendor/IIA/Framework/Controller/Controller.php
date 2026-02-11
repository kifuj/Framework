<?php

namespace IIA\Framework\Controller;

use IIA\Framework\Database\Database;

class Controller
{
    protected string $viewPath;
    protected string $template;
    private Database $database;
    private array $routes = [];

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function setDatabase(Database $database): void
    {
        $this->database = $database;
    }


    public function run(): void {
    
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
                call_user_func($route['action']);
                return;
            }
        }

        $this->show404();
    }

    private function show404(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 404 - Page non trouvée</title>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>La page que vous recherchez n\'existe pas ou a été déplacée.</p>
        <p><a href="/">Retour à l\'accueil</a></p>
    </div>
</body>
</html>';
    }
}