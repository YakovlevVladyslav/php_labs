# Lab6: Обработка и валидация форм
IA2404 Yakovlev Vladyslav

## Цели

Освоить основные принципы работы с HTML-формами в PHP, включая отправку данных на сервер и их обработку, включая валидацию данных.

## Тема проекта: Трекер конфликтов на ближнем востоке.

## Ход работы:

### Шаг 1. Определение модели данных

```
int id;
string title
text description
date startingDate;
enum IsNuclearWeaponInvolved {"yes", "yes, but not officially", "not yet"};
bool IsActive;
```

### Шаг 2. Создание HTML-формы
```
<form method="POST" action="/conflictFormHandler.php">
        <div>
            <label>Title:</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required></textarea>
        </div>
        <div>
            <label>Starting Date:</label>
            <input type="date" name="startingDate" required>
        </div>
        <div>
            <label>Nuclear Involvement:</label>
            <select name="nuclearWeapon">
                <option value="yes">Yes</option>
                <option value="yes, but not officially">Yes, but not officially</option>
                <option value="not yet">Not yet</option>
            </select>
        </div>
        <div>
            <label>
                <input type="checkbox" name="isActive" value="1"> Active Conflict
            </label>
        </div>
        <button type="submit">Initialize Conflict Object</button>
    </form>
```

### Шаг 3. Обработка данных на сервере

Логика обработки вынесена в conflict form handler с классом ConflictHandler

### Шаг 4. Вывод данных

При получении новых данных отрисовывается таблица в index.php
```
$folder = 'conflicts';

    if (is_dir($folder)) {
        $files = glob($folder . '/*.json');
        $conflictFiles = array_filter($files, function($file) {
            return basename($file) !== 'last_id.json';
        });

        if (!empty($conflictFiles)) {
            echo '<div class="result">';
            echo '<h2>Stored Conflicts</h2>';
            // Added an ID to the table for JavaScript targeting
            echo '<table id="conflictTable" border="1" style="width:100%; border-collapse: collapse; text-align: left;">';
            echo '<tr style="background-color: #eee;">
                    <th style="padding: 8px; cursor: pointer;" onclick="sortTable(0)">ID <span>▼</span></th>
                    <th style="padding: 8px;">Title</th>
                    <th style="padding: 10px; cursor: pointer;" onclick="sortTable(2)">Date <span>▼</span></th>
                    <th style="padding: 8px;">Nuclear</th>
                    <th style="padding: 8px;">Status</th>
                  </tr>';

            foreach ($conflictFiles as $file) {
                $jsonData = file_get_contents($file);
                $conflict = json_decode($jsonData, true);

                if ($conflict) {
                    $status = $conflict['active'] ? 'Active' : 'Resolved';
                    echo '<tr>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars((string)$conflict['id']) . '</td>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars($conflict['title']) . '</td>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars($conflict['date']) . '</td>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars($conflict['nuclear']) . '</td>';
                    echo '<td style="padding: 8px;">' . $status . '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            echo '</div>';
        }
    }
```

### Шаг 5. Дополнительные функции. ООП-реализация

Код приложения разбит на класс Conflict, содержащий данные, скрипт conflictFormHandler с классом ConflictHandler хранящие только логику обработки поступивших данных из формы, и представление пользователю в скрипте index.php 

## Контрольные вопросы:
```
1. Методы отправки данных
Существует несколько HTTP-методов (глаголов), но наиболее распространенными являются:

GET: Данные передаются в URL в виде пар «ключ=значение» после знака вопроса (например, search.php?query=php).

POST: Данные передаются в теле HTTP-запроса, что делает их невидимыми в адресной строке.

PUT/PATCH: Используются для обновления существующих данных.

DELETE: Используется для удаления ресурсов.

Какие методы поддерживает HTML-форма?
Стандарт HTML поддерживает только два метода для атрибута method в теге <form>:

GET: Используется для получения данных или простых поисковых запросов. Данные кэшируются и имеют ограничение по длине.

POST: Используется для создания/отправки чувствительных данных (пароли, файлы) или больших объемов информации. Данные не кэшируются.

2. Глобальные переменные в PHP
PHP автоматически собирает данные формы в специальные суперглобальные массивы в зависимости от метода отправки:

$_GET: Ассоциативный массив данных, переданных через URL (метод GET).

$_POST: Ассоциативный массив данных, переданных в теле запроса (метод POST).

$_REQUEST: Объединенный массив, который по умолчанию содержит содержимое $_GET, $_POST и $_COOKIE.

$_FILES: Используется для доступа к загруженным файлам (требует enctype="multipart/form-data" в форме).

3. Безопасность и защита от XSS
XSS (Cross-Site Scripting) возникает, когда вредоносный скрипт, отправленный пользователем, отображается на странице без фильтрации.

Основные методы защиты:
Экранирование вывода (Escaping):
Самый надежный способ. Перед выводом любых данных в браузер используйте функцию htmlspecialchars(). Она превращает символы вроде < и > в безопасные HTML-сущности (&lt; и &gt;), предотвращая выполнение скриптов.

Валидация данных:
Всегда проверяйте, соответствуют ли данные ожидаемому формату. Например, если вы ждете дату, проверьте её через класс DateTime, как это сделано в вашем обработчике.

Фильтрация (Sanitization):
Используйте встроенную функцию filter_var(). Она позволяет очистить строку от нежелательных символов или проверить e-mail/URL.

Content Security Policy (CSP):
Настройка заголовков сервера, которые указывают браузеру, из каких источников разрешено загружать и исполнять скрипты.

Защита от CSRF:
Хотя это не XSS, это важная часть безопасности форм. Используйте скрытые токены в формах, чтобы убедиться, что запрос пришел именно с вашего сайта.
```