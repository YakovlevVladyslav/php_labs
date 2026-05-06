<?php
// app/src/Core/Router.php

namespace App\Core;

/**
 * Простейший маршрутизатор (роутер) приложения.
 * * Регистрирует доступные маршруты для GET и POST запросов, сопоставляет
 * текущий URI с зарегистрированными путями и вызывает соответствующее действие контроллера.
 */
class Router {
    
    /**
     * Ассоциативный массив зарегистрированных маршрутов.
     * * Структура: ['GET' => ['/path' => [ControllerClass, 'method']], 'POST' => [...]]
     * @var array
     */
    private array $routes = [];

    /**
     * Регистрирует обработчик для GET-запроса по указанному пути.
     * * @param string $path URL-путь (например, '/login').
     * @param array $handler Массив, где первый элемент — имя класса контроллера, второй — метод.
     * @return void
     */
    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Регистрирует обработчик для POST-запроса по указанному пути.
     * * @param string $path URL-путь (например, '/register').
     * @param array $handler Массив, где первый элемент — имя класса контроллера, второй — метод.
     * @return void
     */
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Выполняет маршрутизацию текущего входящего HTTP-запроса.
     * * Парсит входящий URI, отсекает GET-параметры запроса, проверяет наличие
     * зарегистрированного маршрута, динамически инициализирует контроллер и вызывает его метод.
     * В случае отсутствия совпадений отдает HTTP-статус 404.
     * * @param string $method Метод HTTP-запроса (GET, POST).
     * @param string $uri Полный входящий URI запроса.
     * @return void
     */
    public function handle(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $action] = $this->routes[$method][$path];

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Страница не найдена</h1>";
    }
}