<?php

/**
 * Class Send email
 */
final class SendEmail extends CI_Controller
{
    /**
     * Construct
     */
    function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            echo "This controller can only be accessed from the command line interface (CLI).\n";
            exit;
        }

        parent::__construct();
        $this->load->service('EmailService');
    }

    /**
     * Send email
     *
     * @return void
     */
    function sendEmail(): void
    {
        $this->EmailService->sendEmail();
    }
}