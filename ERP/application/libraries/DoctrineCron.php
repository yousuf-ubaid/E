<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

final class DoctrineCron
{
    /**
     * Configuration
     *
     * @var Configuration
     */
    private Configuration $config;

    /**
     * Construct
     */
    public function __construct()
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([APPPATH . 'src/entities']);
        $config->setAutoGenerateProxyClasses(true);
        $this->config = $config;
    }

    /**
     * Get entity manager
     *
     * @param array $connection
     * @return EntityManager
     */
    public function getEntityManager(array $connection): EntityManager
    {
        $connection = DriverManager::getConnection($connection, $this->config);
        return new EntityManager($connection, $this->config);
    }

}
