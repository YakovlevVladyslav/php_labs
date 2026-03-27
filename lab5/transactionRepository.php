<?php

declare(strict_types=1);

require_once 'transaction.php';
require_once 'TransactionStorageInterface.php';

/**
 * Class TransactionRepository
 * * An in-memory implementation of TransactionStorageInterface.
 * This class manages a collection of Transaction objects within an array.
 */
class TransactionRepository implements TransactionStorageInterface {
    /**
     * @var Transaction[] Internal collection of transactions.
     */
    private array $transactions = [];

    /**
     * Adds a new transaction to the internal collection.
     * * @param Transaction $transaction The transaction object to store.
     * @return void
     */
    public function addTransaction(Transaction $transaction): void {
        $this->transactions[] = $transaction;
    }

    /**
     * Removes a transaction from the collection by its ID.
     * After removal, the array keys are reset to maintain a continuous index.
     * * @param int $id The unique identifier of the transaction to remove.
     * @return void
     */
    public function removeTransactionById(int $id): void {
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction->getId() === $id) {
                unset($this->transactions[$key]);
                // Re-index the array to prevent gaps in numeric keys
                $this->transactions = array_values($this->transactions);
                break;
            }
        }
    }

    /**
     * Returns the full list of stored transactions.
     * * @return Transaction[] An array of Transaction objects.
     */
    public function getAllTransactions(): array {
        return $this->transactions;
    }

    /**
     * Finds and returns a transaction by its unique ID.
     * * @param int $id The ID to search for.
     * @return Transaction|null The found transaction or null if no match is found.
     */
    public function findById(int $id): ?Transaction {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }
        return null;
    }
}