<?php

declare(strict_types=1);

namespace App\Src\EventSubscriber;

use App\Src\Interface\AuditableEntityInterface;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

/**
 * Subscriber for audit
 *
 * @package App\Listener
 */
final class AuditEntitySubscriber implements EventSubscriber
{
    /**
     * Ci
     *
     * @var \CI_Controller $ci
     */
    private \CI_Controller $ci;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->ci =& get_instance();
    }

    /**
     * Subscribe
     *
     * @return array|string[]
     */
    public function getSubscribedEvents(): array
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }

    /**
     * Pre persist
     *
     * @param PrePersistEventArgs $args
     * @return void
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof AuditableEntityInterface) {
            return;
        }

        $entity->setCreatedDateTime(new DateTime());
        $entity->setCreatedPCID($this->ci->common_data['current_pc']);
        $entity->setCreatedUserID($this->ci->common_data['current_userID']);
        $entity->setCreatedUserName($this->ci->common_data['current_user']);
    }

    /**
     * Pre update
     *
     * @param PreUpdateEventArgs $args
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof AuditableEntityInterface) {
            return;
        }

        $entity->setModifiedDateTime(new DateTime());
        $entity->setModifiedPCID($this->ci->common_data['current_pc']);
        $entity->setModifiedUserID($this->ci->common_data['current_userID']);
        $entity->setModifiedUserName($this->ci->common_data['current_user']);
    }

}

