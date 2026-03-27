<?php

declare(strict_types=1);

/**
 * Class Merchant
 * * Represents the recipient of a payment transaction.
 */
class Merchant {
    /**
     * Merchant constructor.
     * * @param string $name The name of the merchant or recipient.
     * @param string $category The business category (e.g., "Food", "Tech").
     */
    public function __construct(
        private string $name,
        private string $category
    ) {}

    /**
     * Get the name of the merchant.
     * * @return string
     */
    public function getName(): string { 
        return $this->name; 
    }

    /**
     * Get the category of the merchant.
     * * @return string
     */
    public function getCategory(): string { 
        return $this->category; 
    }
}

/**
 * Class Transaction
 * * Describes a single bank transaction including its metadata and logic.
 */
class Transaction {
    /** @var int Unique identifier for the transaction. */
    private int $id;

    /** @var DateTime The date when the transaction occurred. */
    private DateTime $date;

    /** @var string The monetary amount as a string for precision. */
    private string $amount;

    /** @var string Description of the payment purpose. */
    private string $description;

    /** @var Merchant The entity receiving the payment. */
    private Merchant $merchant;

    /**
     * Transaction constructor.
     * * @param int $id Unique ID.
     * @param DateTime $date Date of transaction.
     * @param string $amount Transaction amount.
     * @param string $description Payment details.
     * @param Merchant $merchant Recipient object.
     */
    public function __construct(int $id, DateTime $date, string $amount, string $description, Merchant $merchant) {
        $this->id = $id;
        $this->date = $date;
        $this->amount = $amount;
        $this->description = $description;
        $this->merchant = $merchant;
    }

    /**
     * Get the transaction unique identifier.
     * * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get the transaction date.
     * * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * Get the transaction amount.
     * * @return string
     */
    public function getAmount(): string {
        return $this->amount;
    }

    /**
     * Get the payment description.
     * * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Get the merchant object.
     * * @return Merchant
     */
    public function getMerchant(): Merchant {
        return $this->merchant;
    }

    /**
     * Calculates the number of full days elapsed between the transaction date 
     * and the current system time.
     * * @return int Number of days.
     */
    public function getDaysSinceTransaction(): int {
        $currentDate = new DateTime(); 
        $interval = $currentDate->diff($this->date);

        // %a returns the absolute total number of days difference
        return (int) $interval->format('%a');
    }
}