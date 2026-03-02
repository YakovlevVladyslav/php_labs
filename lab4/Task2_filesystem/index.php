<?php
declare(strict_types=1);

// Путь к папке с изображениями
$dir = __DIR__ . '/image/';      // физический путь на сервере
$dirWeb = 'image/';              // путь для браузера (URL)

// Сканируем директорию
$files = scandir($dir);

if ($files === false) {
    $images = [];
} else {
    // Оставляем только .jpg (и .JPG тоже)
    $images = array_filter($files, function ($file) use ($dir) {
        if ($file === '.' || $file === '..') {
            return false;
        }
        // проверка расширения
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext !== 'jpg') {
            return false;
        }
        // на всякий случай — файл должен существовать
        return is_file($dir . $file);
    });

    // Переиндексируем массив
    $images = array_values($images);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        header, footer { padding: 16px 20px; background: #f2f2f2; }
        nav { padding: 10px 20px; background: #e7e7e7; }
        nav a { margin-right: 12px; text-decoration: none; color: #333; }
        main { padding: 20px; }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }
        .card {
            width: 250px;
            height: 200px;
            border: 1px solid #ddd;
            padding: 8px;
            background: #fff;
            box-sizing: border-box;
        }

        .card img {
            width: 100%;
            height: 160px;
            object-fit: contain;
            display: block;
        }
        .caption {
            margin-top: 6px;
            font-size: 12px;
            color: #555;
            word-break: break-word;
        }
        .empty {
            padding: 12px;
            border: 1px dashed #999;
            background: #fafafa;
        }
    </style>
</head>
<body>

<header>
    <h1>Моя фотогалерея</h1>
</header>

<nav>
    <a href="#">Главная</a>
    <a href="#">Галерея</a>
    <a href="#">Контакты</a>
</nav>

<main>
    <h2>Изображения из папки /image</h2>

    <?php if (count($images) === 0): ?>
        <div class="empty">
            В папке <b>image</b> не найдено .jpg файлов. Проверь, что изображения лежат в <code>./image/</code>.
        </div>
    <?php else: ?>
        <div class="gallery">
            <?php foreach ($images as $file): ?>
                <?php
                // Формируем безопасный URL (имя файла может содержать пробелы)
                $url = $dirWeb . rawurlencode($file);
                ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($file) ?>">
                    <div class="caption"><?= htmlspecialchars($file) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    <small>© <?= date('Y') ?> Галерея на PHP</small>
</footer>

</body>
</html>