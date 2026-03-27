<?php

declare(strict_types=1);

require_once 'transaction.php';

/**
 * Interface TransactionStorageInterface
 * * Defines the contract for transaction storage engines.
 * Any class implementing this interface must provide methods for 
 * persisting, retrieving, and managing a collection of Transaction objects.
 */
interface TransactionStorageInterface {
    
    /**
     * Adds a new transaction to the storage.
     * * @param Transaction $transaction The transaction object to be stored.
     * @return void
     */
    public function addTransaction(Transaction $transaction): void;

    /**
     * Removes a transaction from storage by its unique identifier.
     * * @param int $id The unique ID of the transaction to remove.
     * @return void
     */
    public function removeTransactionById(int $id): void;

    /**
     * Retrieves all transactions currently held in storage.
     * * @return Transaction[] An array containing all stored Transaction objects.
     */
    public function getAllTransactions(): array;

    /**
     * Searches for a specific transaction by its unique identifier.
     * * @param int $id The unique ID to search for.
     * @return Transaction|null Returns the Transaction object if found, or null otherwise.
     */
    public function findById(int $id): ?Transaction;
}