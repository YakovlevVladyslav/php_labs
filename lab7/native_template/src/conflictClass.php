<?php

declare(strict_types=1);

/**
 * Represents the status of nuclear weapon involvement in a conflict.
 */
enum IsNuclearWeaponInvolved : string {
    case yes = "yes";
    case maybe = "yes, but not officially";
    case not = "not yet";
}

/**
 * Represents a historical or current geopolitical conflict.
 */
class Conflict {
    /** @var int The unique identifier for the conflict. */
    private int $id;

    /** @var string The name or title of the conflict. */
    private string $title;

    /** @var string A detailed description of the event. */
    private string $description;

    /** @var DateTime The date when the conflict officially began. */
    private DateTime $startingDate;

    /** @var IsNuclearWeaponInvolved The level of nuclear involvement. */
    private IsNuclearWeaponInvolved $nuclearWeapon;

    /** @var bool Indicates if the conflict is currently ongoing. */
    private bool $isActive;

    /**
     * Conflict constructor.
     *
     * @param int $id
     * @param string $title
     * @param string $description
     * @param DateTime $startingDate
     * @param IsNuclearWeaponInvolved $weapon
     * @param bool $isact
     */
    public function __construct(
        int $id, 
        string $title, 
        string $description, 
        DateTime $startingDate, 
        IsNuclearWeaponInvolved $weapon, 
        bool $isact
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->startingDate = $startingDate;
        $this->nuclearWeapon = $weapon;
        $this->isActive = $isact;
    }

    /**
     * Gets the unique ID of the conflict.
     * * @return int
     */
    public function getId(): int {
        return $this->id;
    }
}