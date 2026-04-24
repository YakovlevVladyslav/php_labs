# Lab №7. Шаблонизация: Обработка и валидация форм
IA2404 Yakovlev Vladyslav

## Цели

Освоить принципы шаблонизации в PHP, как с использованием нативных PHP-шаблонов, так и с применением готового шаблонизатора. Улучшить структуру проекта, разделив логику обработки данных и представление.

## Тема проекта: Трекер конфликтов на ближнем востоке.

## Ход работы:

### Шаг 1. Рефакторинг. Нативные PHP-шаблоны

Проект был разделен на структуру 
```
root
    conflicts //jsons data
    
    src
        conflictClass.php
        conflictFormHandler.php

    templates
        conflictList.php
        conflictInputHandler.php
```

### Шаг 2. Использование готовых шаблонизаторов

#### 2.1. Установить и настроить шаблонизатор  Twig, с помощью Composer.

Composer - пакетный менеджер, инструмент для управления зависимостями в php проектах.

Сперва необходимо установить сам Composer с официальной страницы.

Затем корне необходимо открыть терминал и прописать 
```
composer require twig/twig:3.*
```
Это сгенерирует composer.json

#### Шаг 2.2: Создание базового шаблона (Наследование)

В папке templates создать файл base.html.twig . Это позволит использовать наследование шаблонов, чтобы не дублировать head, body и общие стили в каждом файле.

Файл: templates/base.html.twig
``` 
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Conflict Tracker{% endblock %}</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 40px auto; background: #f4f4f9; }
        form, .result { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        div { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="checkbox"] { width: auto; }
        button { background: #2ecc71; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        .result { border-left: 5px solid #3498db; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #eee; cursor: pointer; }
    </style>
</head>
<body>
    <h1>{% block header %}Conflict Data Entry{% endblock %}</h1>
    
    {% block content %}{% endblock %}

    {% block javascripts %}{% endblock %}
</body>
</html>
```


#### Шаг 2.3: Переписывание шаблонов на синтаксис Twig

Необходимо заменить файлы templates/conflict_form.html и templates/dashboard.html на templates/conflict_form.html.twig и templates/dashboard.html.twig

#### Шаг 2.4: Настройка Twig в index.php

Теперь нужно изменить основной файл, чтобы он загружал Twig и передавал в него данные.

```
<?php
require_once 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Настройка загрузчика шаблонов
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

// Логика получения данных (как и была)
$conflicts = [];
$folder = 'conflicts';
if (is_dir($folder)) {
    $files = glob($folder . '/*.json');
    foreach ($files as $file) {
        if (basename($file) === 'last_id.json') continue;
        $data = json_decode(file_get_contents($file), true);
        if ($data) { $conflicts[] = $data; }
    }
}

// Рендерим главный шаблон и передаем массив данных
echo $twig->render('dashboard.html.twig', [
    'conflicts' => $conflicts,
    'error' => $_GET['error'] ?? null
]);
```

### Доп. Задание

Созданы фильтры status_label, который превращает булево значение (active) в красивый текстовый тег, и фильтр nuclear_badge, который добавляет предупреждающий эмодзи для опасных конфликтов.

## Контрольные вопросы

1. PHP-шаблоны vs Twig

Синтаксис: В PHP — <?php echo $var; ?>, в Twig — {{ var }}. Twig лаконичнее.

Безопасность: Twig автоматически защищает от XSS-атак (экранирование), в PHP это нужно делать вручную через htmlspecialchars.

Возможности: Twig поддерживает наследование и фильтры (например, ваши status_label и nuclear_badge), что упрощает верстку.

Производительность: PHP работает напрямую, Twig требует предварительной компиляции, но в работе они практически одинаково быстры.

2. Разделение логики и представления

Зачем: Чтобы код обработки данных (PHP) лежал отдельно от HTML-разметки.

Проблемы смешивания: Возникает «спагетти-код», который трудно читать и тестировать.

Поддержка: Легче менять дизайн, не рискуя сломать логику работы с файлами или объектами Conflict.

Безопасность: Снижается риск случайно вывести необработанные данные в браузер.

3. Наследование в Twig

Позволяет создать один «скелет» сайта и менять в нем только нужные части.

{% extends "base.html.twig" %}: Подключает главный файл-каркас (например, со стилями и шапкой).

{% block name %}:

В базе — это пустое «окно» для контента.

В дочернем шаблоне — это само содержимое (например, ваша таблица или форма), которое вставляется в это «окно».