<?php

declare(strict_types=1);

$transactions = [
    [
        "id" => 1,
        "date" => new DateTime("2019-01-01"),
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => new DateTime("2020-02-15"),
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
];

function calculateTotalAmount(array $transactions): float
{
    $total = 0.0;

    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }

    return $total;
}

function findTransactionByDescription(string $descriptionPart): ?array
{
    global $transactions;

    foreach ($transactions as $transaction) {
        if (stripos($transaction['description'], $descriptionPart) !== false) {
            return $transaction;
        }
    }

    return null; // если ничего не найдено
}

$result = findTransactionByDescription('dinner');

if ($result !== null) {
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}

function findTransactionById(int $id): ?array
{
    global $transactions;

    $filtered = array_filter(
        $transactions,
        fn($transaction) => $transaction['id'] === $id
    );

    if (empty($filtered)) {
        return null;
    }

    // array_filter сохраняет ключи, поэтому берём первый элемент
    return array_values($filtered)[0];
}


if ($result !== null) {
    echo "<h3>Найдена транзакция:</h3>";
    echo "<ul>";
    echo "<li>ID: " . $result['id'] . "</li>";
    echo "<li>Date: " . $result['date']->format('Y-m-d') . "</li>";
    echo "<li>Amount: " . number_format($result['amount'], 2) . "</li>";
    echo "<li>Description: " . htmlspecialchars($result['description']) . "</li>";
    echo "<li>Merchant: " . htmlspecialchars($result['merchant']) . "</li>";
    echo "</ul>";
} else {
    echo "<p>Транзакция не найдена.</p>";
}

function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime();

    $difference = $currentDate->diff($transactionDate);

    return (int) $difference->format('%a');
}

function addTransaction(
    int $id,
    string $date,
    float $amount,
    string $description,
    string $merchant
): void {
    global $transactions;

    foreach ($transactions as $transaction) {
    if ($transaction['id'] === $id) {
        return; // не добавляем дубликат
    }
}

    $transactions[] = [
        "id" => $id,
        "date" => new DateTime($date),
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}

addTransaction(
    3,
    "2024-12-25",
    250.00,
    "Christmas gifts",
    "Amazon"
);

//sort by date
usort($transactions, function ($a, $b) {
    return $a['date'] <=> $b['date'];
});

//sort by amount from less to high
usort($transactions, function ($a, $b) {
    return $b['amount'] <=> $a['amount'];
});


$result = findTransactionById(2);
$totalAmount = calculateTotalAmount($transactions);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transactions</title>
</head>
<body>

<h2>Список транзакций</h2>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Merchant</th>
            <th>Days Ago</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= $transaction['id'] ?></td>
                <td><?= $transaction['date']->format('Y-m-d') ?></td>
                <td><?= number_format($transaction['amount'], 2) ?></td>
                <td><?= htmlspecialchars($transaction['description']) ?></td>
                <td><?= htmlspecialchars($transaction['merchant']) ?></td>
                <td><?= daysSinceTransaction($transaction['date']->format('Y-m-d')) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2"><strong>Total:</strong></td>
            <td><strong><?= number_format($totalAmount, 2) ?></strong></td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

</body>
</html>