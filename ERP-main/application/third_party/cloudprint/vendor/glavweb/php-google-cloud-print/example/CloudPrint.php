<?php

include_once (APPPATH.'/third_party/cloudprint/vendor/autoload.php');
class CloudPrint
{
    private $printerId;
    private $privateKeyPath;
    private $client_email;

    function __construct($pId,$pkp,$cm)
    {
        $this->printerId = $pId;
        $this->privateKeyPath = $pkp;
        $this->client_email = $cm;
    }

    public function printCloud()
    {
        if (!$this->printerId) {
            throw new \Glavweb\GoogleCloudPrint\Exception('Printer ID not defined.');
        }

        if (!is_file(APPPATH.'/third_party/cloudprint/vendor/glavweb/php-google-cloud-print/example/'.$this->privateKeyPath)) {
            throw new \Glavweb\GoogleCloudPrint\Exception('Private Key not found.');
        }

        $privateKey = file_get_contents(APPPATH.'/third_party/cloudprint/vendor/glavweb/php-google-cloud-print/example/'.$this->privateKeyPath);
        $scopes = array('https://www.googleapis.com/auth/cloudprint');

        $credentials = new \Google_Auth_AssertionCredentials(
            $this->client_email,
            $scopes,
            $privateKey
        );

        $client = new \Google_Client();
        $client->setAssertionCredentials($credentials);

        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion();
        }

        $authToken = json_decode($client->getAuth()->getAccessToken())->access_token;

        if (!$authToken) {
            throw new \Glavweb\GoogleCloudPrint\Exception('Cannot login to CloudPrint.');
        }

        $content = 'Any HTML body.';

        $gcp = new \Glavweb\GoogleCloudPrint\GoogleCloudPrint($authToken);
        $response = $gcp->submit(array(
            'printerid' => $this->printerId,
            'title' => "test",
            'content' => $content,
            'contentType' => 'text/html',
            'tag' => "hi",
            'ticket' => json_encode(array(
                'version' => '1.0',
            ))
        ));

        if (!$response->success) {
            throw new \Glavweb\GoogleCloudPrint\Exception('An error occured while printing the doc. Error code:' . $response->errorCode . ', Message:' . $response->message);
        }

        return $response->job->id;
    }

}