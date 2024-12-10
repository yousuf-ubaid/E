<?php

use App\Src\EventSubscriber\AuditEntitySubscriber;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class Doctrine
{
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    public EntityManager $em;

    /**
     * Construct
     */
    public function __construct()
    {
        $ci =& get_instance();

        require APPPATH . 'config/database.php';

        $config = ORMSetup::createAttributeMetadataConfiguration([APPPATH . 'src/entities']);
        $config->setAutoGenerateProxyClasses(true);

        $db2 = $ci->load->database('db2', TRUE);

        $db2->select('*');
        $db2->where("UserName", trim($ci->session->userdata("loginusername") ?? ''));
        $db2->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $db2->get("user")->row_array();

        $connection = $this->getDBConnection($ci, $resultDb2 ?? []);

        $isGroupUser = trim($ci->session->userdata("isGroupUser") ?? '');

        if (1 == $isGroupUser) {
            $company_id = trim($ci->session->userdata("companyID") ?? '');
            
            $db2->select('*');
            $db2->where("company_id", $company_id);
            $resultDb2 = $db2->get("srp_erp_company")->row_array();

            $connection = $this->getDBConnection($ci, $resultDb2);
        }

        $eventManager = new EventManager();
        $eventManager->addEventSubscriber(new AuditEntitySubscriber());

        $connection = DriverManager::getConnection($connection, $config);
        $this->em = new EntityManager($connection, $config, $eventManager);

    }

    /**
     * Get entity manager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    /**
     * Get db connection
     *
     * @param CI_Controller $ci
     * @param array<string, mixed> $data
     * @return array
     */
    public function getDBConnection(CI_Controller $ci, array $data): array
    {
        return [
            'driver'   => 'pdo_mysql',
            'user'     => trim($ci->encryption->decrypt($data["db_username"])),
            'password' => trim($ci->encryption->decrypt($data["db_password"])),
            'dbname'   => trim($ci->encryption->decrypt($data["db_name"])),
            'host'     => trim($ci->encryption->decrypt($data["host"])),
            'charset'  => 'utf8mb4'
        ];
    }
}
