<?php

declare(strict_types=1);

require_once 'transaction.php';
require_once 'transactionStorageInterface.php';
require_once 'transactionRepository.php';
require_once 'transactionManager.php';
require_once 'transactionTableRender.php';


$repository = new TransactionRepository();


$merchants = [
    'amazon' => new Merchant("Amazon", "E-commerce"),
    'starbucks' => new Merchant("Starbucks", "Cafe"),
    'netflix' => new Merchant("Netflix", "Entertainment"),
    'walmart' => new Merchant("Walmart", "Groceries"),
    'apple' => new Merchant("Apple", "Electronics"),
    'shell' => new Merchant("Shell", "Gas Station"),
    'uber' => new Merchant("Uber", "Transport"),
    'airbnb' => new Merchant("Airbnb", "Travel"),
    'steam' => new Merchant("Steam", "Gaming"),
    'gym' => new Merchant("Fitness World", "Health"),
];


$transactionsData = [
    [1, '2023-11-20', '1250.50', 'iPhone 15 Case', $merchants['amazon']],
    [2, '2023-12-05', '15.75', 'Morning Latte', $merchants['starbucks']],
    [3, '2024-01-10', '19.99', 'Monthly Subscription', $merchants['netflix']],
    [4, '2024-01-15', '85.20', 'Weekly Groceries', $merchants['walmart']],
    [5, '2024-02-01', '2499.00', 'MacBook Pro 14', $merchants['apple']],
    [6, '2024-02-14', '60.00', 'Full Tank Gas', $merchants['shell']],
    [7, '2024-02-28', '25.40', 'Ride to Airport', $merchants['uber']],
    [8, '2024-03-05', '450.00', 'Weekend in Paris', $merchants['airbnb']],
    [9, '2024-03-12', '59.99', 'Elden Ring DLC', $merchants['steam']],
    [10, '2024-03-20', '45.00', 'Monthly Membership', $merchants['gym']],
];


foreach ($transactionsData as $data) {
    $transaction = new Transaction(
        $data[0],               // id
        new DateTime($data[1]), // date
        $data[2],               // amount
        $data[3],               // description
        $data[4]                // merchant
    );
    $repository->addTransaction($transaction);
}


$manager = new TransactionManager($repository);

// 4. Инициализируем рендерер
$renderer = new TransactionTableRenderer();

// --- ВЫВОД ДАННЫХ ---

echo "<h1>Управление транзакциями</h1>";

// А. Вывод всех транзакций через репозиторий
echo "<h2>Список всех транзакций</h2>";
echo $renderer->render($repository->getAllTransactions());

// Б. Вывод статистики через менеджер
echo "<h2>Статистика</h2>";
echo "<b>Общая сумма:</b> " . number_format($manager->calculateTotalAmount(), 2) . " USD<br>";
echo "<b>Транзакций в Starbucks:</b> " . $manager->countTransactionsByMerchant("Starbucks") . "<br>";

// В. Сортировка и вывод
echo "<h2>Сортировка по сумме (убывание)</h2>";
$sortedByAmount = $manager->sortTransactionsByAmountDesc();
echo $renderer->render($sortedByAmount);

// Г. Пример работы с датами
echo "<h2>Транзакции за январь 2024</h2>";
$totalJan = $manager->calculateTotalAmountByDateRange('2024-01-01', '2024-01-31');
echo "Сумма за январь: " . number_format($totalJan, 2) . " USD";

/*
КОНТРОЛЬНЫЕ ВОПРОСЫ

    1. Зачем нужна строгая типизация в PHP и как она помогает при разработке?
Строгая типизация (включаемая через declare(strict_types=1);)
заставляет интерпретатор PHP проверять, чтобы типы данных, передаваемые в функции и возвращаемые из них,
в точности соответствовали объявленным.

Предотвращение ошибок: Она выявляет баги на этапе выполнения 
(или при использовании анализаторов кода),
если вы случайно передадите строку (string) туда, где ожидается целое число (int).

Читаемость кода.
Предсказуемость.

    2. Что такое класс в объектно-ориентированном программировании и какие основные компоненты класса вы знаете?

Класс — это шаблон, на основе которого создаются объекты. Он определяет структуру и поведение, которые
будут у всех экземпляров этого класса.

Основные компоненты:

Свойства (Поля): Переменные, хранящие состояние объекта (например, $amount, $date).

Методы: Функции внутри класса, определяющие его поведение (например, getDaysSinceTransaction()).

Конструктор: Специальный метод (__construct), который вызывается автоматически при создании нового объекта для инициализации его свойств.
Деструктор: вызывается при выходе из поля видимости

Модификаторы доступа: Ключевые слова public, private и protected, которые управляют тем, кто может изменять данные внутри объекта.

    3. Объясните, что такое полиморфизм и как он может быть реализован в PHP?

Полиморфизм (от греч. «многообразие форм») — это принцип, позволяющий использовать объекты разных классов с 
одинаковым интерфейсом так, будто они являются экземплярами одного и того же типа.

Реализация в PHP:

Интерфейсы: Несколько классов реализуют один и тот же интерфейс 
(например, TransactionRepository и DatabaseRepository оба реализуют TransactionStorageInterface). 
Коду, который их использует, неважно, как именно хранятся данные — ему важно лишь наличие методов, описанных в 
интерфейсе.

Наследование: Дочерний класс переопределяет метод родительского класса, сохраняя то же имя и параметры, 
но меняя внутреннюю логику.

    4. Что такое интерфейс в PHP и как он отличается от абстрактного класса?
Интерфейс — это чистый «контракт». Он говорит, что класс должен уметь делать, но не говорит, как.

Основные отличия:

Реализация:
Интерфейс Не может содержать программный код или логику. 
Абстрактный класс: Может содержать как абстрактные методы, так и обычные методы с кодом.

Свойства: 
Интерфейс: Не может иметь свойств (переменных), только константы. 
Абстрактный класс: Может иметь свойства (переменные состояния). 

Множественность:
Класс может реализовать много интерфейсов сразу.
И Класс может наследоваться только от одного абстрактного класса.

Цель: 
Интерфейс: Описать общую способность или контракт. 
Абстрактный класс: Создать базу для группы родственных классов.
*/