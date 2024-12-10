<?php

declare(strict_types=1);

namespace App\Event;

final readonly class AssetTransferEvent
{
    /**
     * Construct
     *
     * @param int $id
     */
    public function __construct(
        private int $id
    )
    {
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}
