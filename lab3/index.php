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

echo "<br />";
echo "Task 2 <br />";
//task 2
$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";
}

echo "<br>End of the loop: a = $a, b = $b";


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
