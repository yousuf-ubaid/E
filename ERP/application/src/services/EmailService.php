<?php

declare(strict_types = 1);

namespace App\Src\Services;

use App\Event\EmailEvent;
use App\Exception\ServiceUnavailableException;
use App\Src\Entities\Alert;
use App\Src\Repositories\AlertRepository;
use Doctrine\DBAL\Exception\ConnectionException;

/**
 * Class EmailService
 *
 * @package App\Src\Services
 */
final class EmailService extends Service
{
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->ci->load->library('DoctrineCron');
        $this->ci->load->library('EmailEventService');
    }

    /**
     * Send email
     *
     * @return void
     */
    public function sendEmail(): void
    {
        $db2 = $this->ci->load->database('db2', TRUE);

        $db2->select('*');
        $resultDb2 = $db2->get("srp_erp_company")->result_array();

        foreach ($resultDb2 as $resultDb) {
            $connection = [
                'driver'   => 'pdo_mysql',
                'user'     => trim($this->ci->encryption->decrypt($resultDb["db_username"])),
                'password' => trim($this->ci->encryption->decrypt($resultDb["db_password"])),
                'dbname'   => trim($this->ci->encryption->decrypt($resultDb["db_name"])),
                'host'     => trim($this->ci->encryption->decrypt($resultDb["host"])),
                'charset'  => 'utf8mb4'
            ];

            $entityManager = $this->ci->doctrinecron->getEntityManager($connection);

            try {
                /** @var AlertRepository $alertRepository */
                $alertRepository = $entityManager->getRepository(Alert::class);
            }  catch (ConnectionException $e) {
                error_log('Connection issue |Date: ' . date('Y-m-d H:i:s') . '|Company: ' . $resultDb['company_code'] . PHP_EOL);
                continue;
            }

            $data = $alertRepository->getNotSentEmail();
            foreach ($data as $alert) {
                try {
                    $this->ci->emaileventservice->dispatch(
                        new EmailEvent(
                            (string)$alert->getEmpEmail(),
                            (string)$alert->getEmailSubject(),
                            (string)$alert->getEmailSubject(),
                            (string)$alert->getEmailBody(),
                            ''
                        )
                    );
                    $alert->setIsEmailSend(1);
                    $alert->setSendResponse('Success');
                    $alert->setSendResponseCode(200);

                   error_log('Email sent to Id: ' . $alert->getAlertID() . '|Email: ' . $alert->getEmpEmail() . '|Date: ' . date('Y-m-d H:i:s') . '|Company: ' . $resultDb['company_code'] . PHP_EOL);
                } catch (ServiceUnavailableException $e) {
                    $alert->setSendResponse($e->getMessage());
                    $alert->setSendResponseCode($e->getCode());
                    error_log('Email not sent to Id: ' . $alert->getAlertID() . '|Email: ' . $alert->getEmpEmail() . '|Date: ' . date('Y-m-d H:i:s') . '|Company: ' . $resultDb['company_code'] . PHP_EOL);
                }

                $alertRepository->persist($alert);
            }
        }
    }

}