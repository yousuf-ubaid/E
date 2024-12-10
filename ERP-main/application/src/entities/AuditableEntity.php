<?php

declare(strict_types=1);

namespace App\Src\Entities;

use Doctrine\ORM\Mapping\Column;

/**
 * Base class for entities that need to track creation and modification details.
 */
abstract class AuditableEntity
{
    /**
     * PC or device ID where the record was created.
     * Useful for audit trails to track the origin of the data.
     */
    #[Column(type: "string", length: 45, nullable: true)]
    protected ?string $createdPCID = null;

    /**
     * ID of the user who created the record.
     * Used to track the user responsible for creating this record.
     */
    #[Column(type: "string", length: 45, nullable: true)]
    protected ?string $createdUserID = null;

    /**
     * Date and time when the record was created.
     * This provides a timestamp for when the data was first entered.
     */
    #[Column(type: "datetime", nullable: true)]
    protected ?\DateTime $createdDateTime = null;

    /**
     * Name of the user who created the record.
     * Useful when tracking who initially inputted the data.
     */
    #[Column(type: "string", length: 200, nullable: true)]
    protected ?string $createdUserName = null;

    /**
     * PC or device ID where the record was last modified.
     * Useful for audit trails to track the origin of modifications.
     */
    #[Column(type: "string", length: 45, nullable: true)]
    protected ?string $modifiedPCID = null;

    /**
     * ID of the user who last modified the record.
     * Tracks the last user responsible for modifying this record.
     */
    #[Column(type: "string", length: 45, nullable: true)]
    protected ?string $modifiedUserID = null;

    /**
     * Date and time when the record was last modified.
     * This timestamp indicates the most recent change.
     */
    #[Column(type: "datetime", nullable: true)]
    protected ?\DateTime $modifiedDateTime = null;

    /**
     * Name of the user who last modified the record.
     * Useful for knowing who performed the most recent update.
     */
    #[Column(type: "string", length: 200, nullable: true)]
    protected ?string $modifiedUserName = null;

    /**
     * Sets the created PC ID.
     *
     * @param string|null $createdPCID The created PC ID.
     * @return self
     */
    public function setCreatedPCID(?string $createdPCID): self
    {
        $this->createdPCID = $createdPCID;
        return $this;
    }

    /**
     * Gets the created PC ID.
     *
     * @return string|null The created PC ID.
     */
    public function getCreatedPCID(): ?string
    {
        return $this->createdPCID;
    }

    /**
     * Sets the ID of the user who created the record.
     *
     * @param string|null $createdUserID The user ID.
     * @return self
     */
    public function setCreatedUserID(?string $createdUserID): self
    {
        $this->createdUserID = $createdUserID;
        return $this;
    }

    /**
     * Gets the ID of the user who created the record.
     *
     * @return string|null The user ID.
     */
    public function getCreatedUserID(): ?string
    {
        return $this->createdUserID;
    }

    /**
     * Sets the date and time when the record was created.
     *
     * @param \DateTime|null $createdDateTime The created date and time.
     * @return self
     */
    public function setCreatedDateTime(?\DateTime $createdDateTime): self
    {
        $this->createdDateTime = $createdDateTime;
        return $this;
    }

    /**
     * Gets the date and time when the record was created.
     *
     * @return \DateTime|null The created date and time.
     */
    public function getCreatedDateTime(): ?\DateTime
    {
        return $this->createdDateTime;
    }

    /**
     * Sets the name of the user who created the record.
     *
     * @param string|null $createdUserName The name of the user.
     * @return self
     */
    public function setCreatedUserName(?string $createdUserName): self
    {
        $this->createdUserName = $createdUserName;
        return $this;
    }

    /**
     * Gets the name of the user who created the record.
     *
     * @return string|null The name of the user.
     */
    public function getCreatedUserName(): ?string
    {
        return $this->createdUserName;
    }

    /**
     * Sets the modified PC ID.
     *
     * @param string|null $modifiedPCID The modified PC ID.
     * @return self
     */
    public function setModifiedPCID(?string $modifiedPCID): self
    {
        $this->modifiedPCID = $modifiedPCID;
        return $this;
    }

    /**
     * Gets the modified PC ID.
     *
     * @return string|null The modified PC ID.
     */
    public function getModifiedPCID(): ?string
    {
        return $this->modifiedPCID;
    }

    /**
     * Sets the ID of the user who last modified the record.
     *
     * @param string|null $modifiedUserID The user ID.
     * @return self
     */
    public function setModifiedUserID(?string $modifiedUserID): self
    {
        $this->modifiedUserID = $modifiedUserID;
        return $this;
    }

    /**
     * Gets the ID of the user who last modified the record.
     *
     * @return string|null The user ID.
     */
    public function getModifiedUserID(): ?string
    {
        return $this->modifiedUserID;
    }

    /**
     * Sets the date and time when the record was last modified.
     *
     * @param \DateTime|null $modifiedDateTime The modified date and time.
     * @return self
     */
    public function setModifiedDateTime(?\DateTime $modifiedDateTime): self
    {
        $this->modifiedDateTime = $modifiedDateTime;
        return $this;
    }

    /**
     * Gets the date and time when the record was last modified.
     *
     * @return \DateTime|null The modified date and time.
     */
    public function getModifiedDateTime(): ?\DateTime
    {
        return $this->modifiedDateTime;
    }

    /**
     * Sets the name of the user who last modified the record.
     *
     * @param string|null $modifiedUserName The name of the user.
     * @return self
     */
    public function setModifiedUserName(?string $modifiedUserName): self
    {
        $this->modifiedUserName = $modifiedUserName;
        return $this;
    }

    /**
     * Gets the name of the user who last modified the record.
     *
     * @return string|null The name of the user.
     */
    public function getModifiedUserName(): ?string
    {
        return $this->modifiedUserName;
    }

}
