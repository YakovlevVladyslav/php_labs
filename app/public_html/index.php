<?php
// app/public_html/index.php

// 1. Инициализируем безопасную сессию
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Отображение ошибок (включаем для локальной разработки)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 3. Определяем абсолютный путь к корню нашего приложения (/var/www/html)
define('ROOT_DIR', dirname(__DIR__));

// 4. Подключаем автозагрузчик классов Composer
require_once ROOT_DIR . '/vendor/autoload.php';

// 5. Подключаем базу данных (инициализирует глобальный объект $pdo)
require_once ROOT_DIR . '/config/database.php';

// 6. Импортируем используемые классы
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AppointmentController;
use App\Controllers\AdminController;

// 7. Инициализируем Роутер
$router = new Router();

// --- РЕГИСТРАЦИЯ МАРШРУТОВ (РОУТОВ) ---

// Главная страница
$router->get('/', [HomeController::class, 'index']);

// Аутентификация (Вход, Регистрация, Выход)
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/logout', [AuthController::class, 'logout']);

// Записи на обслуживание (Запись на СТО и Личный Кабинет)
$router->get('/appointment/create', [AppointmentController::class, 'create']);
$router->post('/appointment/create', [AppointmentController::class, 'create']);

$router->get('/my-appointments', [AppointmentController::class, 'index']);

// Панель администратора (Доступ только для роли admin)
$router->get('/admin', [AdminController::class, 'index']);
$router->post('/admin/update-status', [AdminController::class, 'updateStatus']);

// Маршруты для 3 новых функций администратора
$router->post('/admin/create-admin', [AdminController::class, 'createAdmin']); // Создание админа
$router->post('/admin/delete-user', [AdminController::class, 'deleteUser']);   // Удаление пользователя
$router->post('/admin/create-service', [AdminController::class, 'createService']); // Создание новой услуги

$router->post('/admin/delete-service', [AdminController::class, 'deleteService']);

// 8. Запускаем обработку входящего запроса
$router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);