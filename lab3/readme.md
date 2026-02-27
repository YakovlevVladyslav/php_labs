# Lab 3

IA2404 Yakovlev Vladyslav

## Инструкция по запуску проекта

Перейти в папку проекта с файлом index.php

Запустить встроенный сервер PHP:
```
php -S localhost:8000
```

Открыть в браузере:
http://localhost:8000

## Описание лабораторной работы

Познакомиться с использованием условных конструкций и циклов в PHP.

## Краткая документация

Синтаксис тернарного оператора: (condition) ? true : false;

## Ход работы

### Условные конструкции

Используя функцию date(), создать таблицу с расписанием, формируемым на основе текущего дня недели.

```
<?php

$dayOfWeek = date('N');

// John Styles
if ($dayOfWeek == 1 || $dayOfWeek == 3 || $dayOfWeek == 5) {
    $johnSchedule = "8:00-12:00";
} else {
    $johnSchedule = "Нерабочий день";
}

// Jane Doe
if ($dayOfWeek == 2 || $dayOfWeek == 4 || $dayOfWeek == 6) {
    $janeSchedule = "12:00-16:00";
} else {
    $janeSchedule = "Нерабочий день";
}

echo "Расписание на " . date('d.m.Y') . "<br><br>";
echo "№ | Фамилия Имя   | График работы<br>";
echo "-----------------------------------<br>";
echo "1 | John Styles   | $johnSchedule<br>";
echo "2 | Jane Doe      | $janeSchedule<br>";
```
### Циклы

Создать файл index.php со следующим кодом:

```
<?php

$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
   $a += 10;
   $b += 5;
}

echo "End of the loop: a = $a, b = $b";
```

Добавьте вывод промежуточных значений $a и $b на каждом шаге цикла.
```
<?php

$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";
}

echo "<br>End of the loop: a = $a, b = $b";
```


```
echo "<br />";
echo "<br />";
echo "While <br />";
//while
$a = 0;
$b = 0;
$i = 0;

while ($i <= 5) {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";

    $i++;
}

echo "<br>End of the loop: a = $a, b = $b";

echo "<br />";
echo "<br />";
echo "Do While <br />";
//do while
$a = 0;
$b = 0;
$i = 0;

do {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";

    $i++;
} while ($i <= 5);

echo "<br>End of the loop: a = $a, b = $b";
```
Перепишите этот цикл, используя оператор while и do-while.
```
echo "<br />";
echo "<br />";
echo "While <br />";
//while
$a = 0;
$b = 0;
$i = 0;

while ($i <= 5) {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";

    $i++;
}

echo "<br>End of the loop: a = $a, b = $b";

echo "<br />";
echo "<br />";
echo "Do While <br />";
//do while
$a = 0;
$b = 0;
$i = 0;

do {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";

    $i++;
} while ($i <= 5);

echo "<br>End of the loop: a = $a, b = $b";
```

## Контрольные вопросы

1. В чем разница между циклами for, while и do-while? В каких случаях лучше использовать каждый из них?

Используется, когда заранее известно количество повторений.

2. Как работает тернарный оператор ? : в PHP?

Это сокращённая форма if...else. condition ? true : false;

3. Что произойдет, если в do-while поставить условие, которое изначально ложно?

тело выполнится один раз, т.к. проверка постфактум