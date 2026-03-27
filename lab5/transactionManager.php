<?php

declare(strict_types=1);

require_once 'transaction.php';
require_once 'TransactionStorageInterface.php';

/**
 * Class TransactionManager
 * * Provides high-level business logic for processing and analyzing transactions.
 * It interacts with a storage engine via TransactionStorageInterface.
 */
class TransactionManager {

    /**
     * TransactionManager constructor.
     * * @param TransactionStorageInterface $repository Any storage implementation (In-memory, DB, etc.)
     */
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    /**
     * Calculates the total sum of all transactions in the repository.
     * * @return float The sum of all transaction amounts.
     */
    public function calculateTotalAmount(): float {
        $total = 0.0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += (float) $transaction->getAmount();
        }
        return $total;
    }

    /**
     * Calculates the total sum of transactions that occurred within a specific date range.
     * * @param string $startDate Start date in a string format (e.g., 'YYYY-MM-DD').
     * @param string $endDate End date in a string format (e.g., 'YYYY-MM-DD').
     * @return float The sum of amounts within the range.
     */
    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float {
        $total = 0.0;
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $date = $transaction->getDate();
            if ($date >= $start && $date <= $end) {
                $total += (float) $transaction->getAmount();
            }
        }
        return $total;
    }

    /**
     * Counts the number of transactions associated with a specific merchant name.
     * * @param string $merchantName The exact name of the merchant to filter by.
     * @return int The total count of matching transactions.
     */
    public function countTransactionsByMerchant(string $merchantName): int {
        $count = 0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            if ($transaction->getMerchant()->getName() === $merchantName) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Sorts all transactions by date in ascending order (oldest to newest).
     * * @return Transaction[] A sorted array of Transaction objects.
     */
    public function sortTransactionsByDate(): array {
        $transactions = $this->repository->getAllTransactions();
        
        usort($transactions, function (Transaction $a, Transaction $b) {
            return $a->getDate() <=> $b->getDate();
        });

        return $transactions;
    }

    /**
     * Sorts all transactions by their monetary amount in descending order.
     * * @return Transaction[] A sorted array of Transaction objects (highest amount first).
     */
    public function sortTransactionsByAmountDesc(): array {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b) {
            return (float)$b->getAmount() <=> (float)$a->getAmount();
        });

        return $transactions;
    }
}