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

    public function run(): void
    {
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

    private function showViewError(string $viewName, string $viewPath): void
    {
        http_response_code(500);
        echo '<!DOCTYPE html>
<html lang="fr">
    ...
    <h1>Erreur 500</h1>
    <h2>Vue introuvable</h2>
    ...
</html>';
    }

    protected function render(string $view, array $variables = []): void
    {
        $viewFile = $this->viewPath . $view . '.html.php';
        $templateFile = $this->viewPath . $this->template . '.html.php';

        if (!file_exists($viewFile)) {
            $this->showViewError($view, $viewFile);
            return;
        }

        if (!file_exists($templateFile)) {
            $this->showViewError($this->template, $templateFile);
            return;
        }

        ob_start();
        extract($variables);
        require ($viewFile);
        $content = ob_get_clean();
        require ($templateFile);
    }

    protected function redirect(string $url): void
{
    header('Location: ' . $url);
    exit();
}
}
