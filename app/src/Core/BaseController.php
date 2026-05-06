<?php
// app/src/Core/BaseController.php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Базовый абстрактный контроллер приложения.
 * * Предоставляет дочерним контроллерам унифицированный доступ к 
 * шаблонизатору Twig и объекту подключения к базе данных PDO.
 */
abstract class BaseController {
    
    /**
     * Среда шаблонизатора Twig.
     * @var Environment
     */
    protected Environment $twig;
    
    /**
     * Экземпляр подключения к базе данных PDO.
     * @var \PDO
     */
    protected \PDO $db;

    /**
     * Конструктор базового контроллера.
     * * Настраивает файловый загрузчик Twig, отключает кэширование для удобства разработки,
     * внедряет глобальные переменные сессии в шаблоны и связывает локальное свойство
     * с глобальным объектом PDO подключения к базе данных.
     */
    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../../views');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);

        $this->twig->addGlobal('session', $_SESSION);

        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Вспомогательный метод для рендеринга Twig-шаблонов.
     * * Компилирует переданный шаблон с входным набором данных и выводит результат в поток ответа.
     * * @param string $template Путь к файлу шаблона относительно директории views.
     * @param array $data Ассоциативный массив переменных, передаваемых в шаблон.
     * @return void
     */
    protected function render(string $template, array $data = []): void {
        echo $this->twig->render($template, $data);
    }
}