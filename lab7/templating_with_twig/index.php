<?php
require_once 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter; // Импортируем класс для фильтров

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

// 1. Фильтр для статуса (Активен/Завершен)
$twig->addFilter(new TwigFilter('status_label', function ($isActive) {
    return $isActive ? '🔴 Active' : '🟢 Resolved';
}));

// 2. Фильтр для ядерного оружия (добавляет иконку, если вовлечено)
$twig->addFilter(new TwigFilter('nuclear_badge', function ($value) {
    if ($value === 'yes') {
        return "☢️ " . strtoupper($value);
    }
    return $value;
}));

// Логика получения данных из файлов (без изменений)
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

echo $twig->render('dashboard.html.twig', [
    'conflicts' => $conflicts
]);