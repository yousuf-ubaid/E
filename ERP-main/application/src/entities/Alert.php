<?php

declare(strict_types=1);

namespace App\Src\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Represents an ERP Alert entity in the system.
 *
 * @package App\Src\Entities
 */
#[Entity(repositoryClass: "App\Src\Repositories\AlertRepository")]
#[Table(name: "srp_erp_alert")]
class Alert
{
    /**
     * Unique identifier for the ERP Alert.
     */
    #[Id]
    #[GeneratedValue(strategy: "AUTO")]
    #[Column(type: "integer")]
    private int $alertID;

    /**
     * The company ID associated with the alert.
     */
    #[Column(type: "string", length: 200, nullable: true)]
    private ?string $companyID;

    /**
     * The employee ID associated with the alert.
     */
    #[Column(type: "string", length: 200, nullable: true)]
    private ?string $empID;

    /**
     * The document ID associated with the alert.
     */
    #[Column(type: "string", length: 100, nullable: true)]
    private ?string $documentID;

    /**
     * The document system code.
     */
    #[Column(type: "integer", nullable: true)]
    private ?int $documentSystemCode;

    /**
     * The document code associated with the alert.
     */
    #[Column(type: "string", length: 100, nullable: true)]
    private ?string $documentCode;

    /**
     * The employee's name related to the alert.
     */
    #[Column(type: "string", length: 500, nullable: true)]
    private ?string $empName;

    /**
     * The employee's email associated with the alert.
     */
    #[Column(type: "string", length: 550, nullable: true)]
    private ?string $empEmail;

    /**
     * CC email addresses for the alert.
     */
    #[Column(type: "string", length: 550, nullable: true)]
    private ?string $ccEmailID;

    /**
     * The subject of the email to be sent with the alert.
     */
    #[Column(type: "text", nullable: true)]
    private ?string $emailSubject;

    /**
     * The body of the email to be sent with the alert.
     */
    #[Column(type: "text", nullable: true)]
    private ?string $emailBody;

    /**
     * Indicates whether the email has been sent (0 for no, 1 for yes).
     */
    #[Column(type: "integer", options: ["default" => 0])]
    private int $isEmailSend;

    /**
     * The timestamp of when the alert was created or updated.
     */
    #[Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $timeStamp;

    /**
     * The response from the email sending process.
     */
    #[Column(type: "text", nullable: true)]
    private ?string $sendResponse;

    /**
     * The response code from the email sending process.
     */
    #[Column(type: "integer", nullable: true)]
    private ?int $sendResponseCode;

    /**
     * The type of the alert.
     */
    #[Column(type: "string", length: 255, nullable: true)]
    private ?string $type;

    /**
     * Gets the alert ID.
     *
     * @return int
     */
    public function getAlertID(): int
    {
        return $this->alertID;
    }

    /**
     * Sets the alert ID.
     *
     * @param int $alertID
     * @return self
     */
    public function setAlertID(int $alertID): self
    {
        $this->alertID = $alertID;
        return $this;
    }

    /**
     * Gets the company ID.
     *
     * @return string|null
     */
    public function getCompanyID(): ?string
    {
        return $this->companyID;
    }

    /**
     * Sets the company ID.
     *
     * @param string|null $companyID
     * @return self
     */
    public function setCompanyID(?string $companyID): self
    {
        $this->companyID = $companyID;
        return $this;
    }

    /**
     * Gets the employee ID.
     *
     * @return string|null
     */
    public function getEmpID(): ?string
    {
        return $this->empID;
    }

    /**
     * Sets the employee ID.
     *
     * @param string|null $empID
     * @return self
     */
    public function setEmpID(?string $empID): self
    {
        $this->empID = $empID;
        return $this;
    }

    /**
     * Gets the document ID.
     *
     * @return string|null
     */
    public function getDocumentID(): ?string
    {
        return $this->documentID;
    }

    /**
     * Sets the document ID.
     *
     * @param string|null $documentID
     * @return self
     */
    public function setDocumentID(?string $documentID): self
    {
        $this->documentID = $documentID;
        return $this;
    }

    /**
     * Gets the document system code.
     *
     * @return int|null
     */
    public function getDocumentSystemCode(): ?int
    {
        return $this->documentSystemCode;
    }

    /**
     * Sets the document system code.
     *
     * @param int|null $documentSystemCode
     * @return self
     */
    public function setDocumentSystemCode(?int $documentSystemCode): self
    {
        $this->documentSystemCode = $documentSystemCode;
        return $this;
    }

    /**
     * Gets the document code.
     *
     * @return string|null
     */
    public function getDocumentCode(): ?string
    {
        return $this->documentCode;
    }

    /**
     * Sets the document code.
     *
     * @param string|null $documentCode
     * @return self
     */
    public function setDocumentCode(?string $documentCode): self
    {
        $this->documentCode = $documentCode;
        return $this;
    }

    /**
     * Gets the employee's name.
     *
     * @return string|null
     */
    public function getEmpName(): ?string
    {
        return $this->empName;
    }

    /**
     * Sets the employee's name.
     *
     * @param string|null $empName
     * @return self
     */
    public function setEmpName(?string $empName): self
    {
        $this->empName = $empName;
        return $this;
    }

    /**
     * Gets the employee's email.
     *
     * @return string|null
     */
    public function getEmpEmail(): ?string
    {
        return $this->empEmail;
    }

    /**
     * Sets the employee's email.
     *
     * @param string|null $empEmail
     * @return self
     */
    public function setEmpEmail(?string $empEmail): self
    {
        $this->empEmail = $empEmail;
        return $this;
    }

    /**
     * Gets the CC email addresses.
     *
     * @return string|null
     */
    public function getCcEmailID(): ?string
    {
        return $this->ccEmailID;
    }

    /**
     * Sets the CC email addresses.
     *
     * @param string|null $ccEmailID
     * @return self
     */
    public function setCcEmailID(?string $ccEmailID): self
    {
        $this->ccEmailID = $ccEmailID;
        return $this;
    }

    /**
     * Gets the email subject.
     *
     * @return string|null
     */
    public function getEmailSubject(): ?string
    {
        return $this->emailSubject;
    }

    /**
     * Sets the email subject.
     *
     * @param string|null $emailSubject
     * @return self
     */
    public function setEmailSubject(?string $emailSubject): self
    {
        $this->emailSubject = $emailSubject;
        return $this;
    }

    /**
     * Gets the email body.
     *
     * @return string|null
     */
    public function getEmailBody(): ?string
    {
        return $this->emailBody;
    }

    /**
     * Sets the email body.
     *
     * @param string|null $emailBody
     * @return self
     */
    public function setEmailBody(?string $emailBody): self
    {
        $this->emailBody = $emailBody;
        return $this;
    }

    /**
     * Gets whether the email has been sent.
     *
     * @return int
     */
    public function getIsEmailSend(): int
    {
        return $this->isEmailSend;
    }

    /**
     * Sets whether the email has been sent.
     *
     * @param int $isEmailSend
     * @return self
     */
    public function setIsEmailSend(int $isEmailSend): self
    {
        $this->isEmailSend = $isEmailSend;
        return $this;
    }

    /**
     * Gets the timestamp of the alert.
     *
     * @return \DateTimeInterface|null
     */
    public function getTimeStamp(): ?\DateTimeInterface
    {
        return $this->timeStamp;
    }

    /**
     * Sets the timestamp of the alert.
     *
     * @param \DateTimeInterface|null $timeStamp
     * @return self
     */
    public function setTimeStamp(?\DateTimeInterface $timeStamp): self
    {
        $this->timeStamp = $timeStamp;
        return $this;
    }

    /**
     * Gets the send response.
     *
     * @return string|null
     */
    public function getSendResponse(): ?string
    {
        return $this->sendResponse;
    }

    /**
     * Sets the send response.
     *
     * @param string|null $sendResponse
     * @return self
     */
    public function setSendResponse(?string $sendResponse): self
    {
        $this->sendResponse = $sendResponse;
        return $this;
    }

    /**
     * Gets the send response code.
     *
     * @return int|null
     */
    public function getSendResponseCode(): ?int
    {
        return $this->sendResponseCode;
    }

    /**
     * Sets the send response code.
     *
     * @param int|null $sendResponseCode
     * @return self
     */
    public function setSendResponseCode(?int $sendResponseCode): self
    {
        $this->sendResponseCode = $sendResponseCode;
        return $this;
    }

    /**
     * Gets the alert type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Sets the alert type.
     *
     * @param string|null $type
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
