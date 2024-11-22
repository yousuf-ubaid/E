<?php

declare(strict_types=1);

use App\Event\AssetTransferEvent;
use App\Event\EmailEvent;
use App\Listener\AssetTransferConfirmListener;
use App\Listener\EmailListener;
use League\Event\EventDispatcher;

final class EventService
{
    /**
     * Event dispatcher
     *
     * @var EventDispatcher
     */
    protected EventDispatcher $dispatcher;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->dispatcher = new EventDispatcher();

        // Register all listeners here
        $this->dispatcher->subscribeTo(EmailEvent::class, new EmailListener());
        $this->dispatcher->subscribeTo(AssetTransferEvent::class, new AssetTransferConfirmListener());
    }

    /**
     * Dispatch
     *
     * @param object $event
     * @return object
     */
    public function dispatch(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }
}

