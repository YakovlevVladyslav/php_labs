<?php

declare(strict_types=1);

require_once 'transactionRepository.php';

/**
 * Класс помечен как final, так как он не предназначен для наследования.
 */
final class TransactionTableRenderer {
    /**
     * Формирует HTML-таблицу на основе массива транзакций.
     * * @param Transaction[] $transactions
     * @return string
     */
    public function render(array $transactions): string {
        if (empty($transactions)) {
            return "<p>Транзакции не найдены.</p>";
        }

        $html = "<table border='1' cellpadding='10' cellspacing='0'>";
        $html .= "<thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата</th>
                        <th>Сумма</th>
                        <th>Описание</th>
                        <th>Получатель</th>
                        <th>Категория</th>
                        <th>Дней прошло</th>
                    </tr>
                  </thead>";
        $html .= "<tbody>";

        foreach ($transactions as $transaction) {
            $merchant = $transaction->getMerchant();
            
            $html .= "<tr>";
            $html .= "<td>" . $transaction->getId() . "</td>";
            // Форматируем дату для удобного чтения
            $html .= "<td>" . $transaction->getDate()->format('Y-m-d') . "</td>";
            $html .= "<td>" . $transaction->getAmount() . "</td>";
            $html .= "<td>" . $transaction->getDescription() . "</td>";
            $html .= "<td>" . $merchant->getName() . "</td>";
            $html .= "<td>" . $merchant->getCategory() . "</td>";
            $html .= "<td>" . $transaction->getDaysSinceTransaction() . "</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody>";
        $html .= "</table>";

        return $html;
    }
}