<?php

declare(strict_types=1);

namespace App\Event;

final readonly class EmailEvent
{
    /**
     * Construct
     *
     * @param string $to
     * @param string $subject
     * @param string $title
     * @param string $body
     * @param string $token
     */
    public function __construct(
        private string $to,
        private string $subject,
        private string $title,
        private string $body,
        private string $token
    )
    {
    }

    /**
     * Get to
     *
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * Get Subject
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

}
