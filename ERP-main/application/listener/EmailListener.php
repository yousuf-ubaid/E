<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\EmailEvent;
use App\Exception\ServiceUnavailableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\Event\Listener;

/**
 * Listener for email
 *
 * @package App\Listener
 */
class EmailListener implements Listener
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
     * Invoke
     *
     * @param object $event
     * @throws ServiceUnavailableException
     * @return void
     */
    public function __invoke(object $event): void
    {
        if (!($event instanceof EmailEvent)) {
            return;
        }

        $client = new Client([
            'base_uri' => $this->ci->config->item('from_email_url'),
            'verify' => false,
        ]);

        try {
            $client->request('POST', '/erpmail', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body'    => $this->getBody($event),
            ]);

        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage());
        }
    }

    /**
     * Get formatted body
     *
     * @param EmailEvent $event
     * @return false|string
     */
    private function getBody(EmailEvent $event): false|string
    {
        $body = [
            'header' => [
                [
                    'subject'    => $event->getSubject(),
                    'to_address' => $event->getTo(),
                    'title'      => $event->getTitle(),
                    'email_body' => $event->getBody(),
                    'ending'     => '',
                    'token_id'   => "{$event->getToken()}"
                ]
            ]
        ];

        return json_encode($body);
    }


}

