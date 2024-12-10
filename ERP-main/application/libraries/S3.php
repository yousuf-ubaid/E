<?php

declare(strict_types=1);

defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

/**
 * Aws s3 bucket class
 */
final class S3
{
    /**
     * CodeIgniter instance.
     *
     * @var object
     */
    private object $ci;

    /**
     * AWS S3 Client instance.
     *
     * @var S3Client
     */
    private S3Client $s3Client;

    /**
     * AWS S3 Bucket name.
     *
     * @var string
     */
    private string $bucket;

    /**
     * Version of the S3 Client.
     * 
     * @var string
     */
    public const string VERSION = 'latest';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->config('s3');

        // Get S3 configuration from config file
        $config = [
            'version'     => self::VERSION,
            'region'      => $this->ci->config->item('region'),
            'credentials' => [
                'key'    => $this->ci->config->item('access_key'),
                'secret' => $this->ci->config->item('secret_key'),
            ],
        ];

        if (ENVIRONMENT === 'development') {
            $config['http'] = [
                'verify' => false,
            ];
        }

        $this->bucket = $this->ci->config->item('bucket');
        $this->s3Client = new S3Client($config);
    }

    /**
     * Upload a file to S3
     *
     * @param string $filePath
     * @param string $s3Path
     * @param string $acl
     * @throws Exception
     * @return string
     */
    public function upload(
        string $filePath,
        string $s3Path,
        string $acl = 'public-read'
    ): string {
        try {
            $config = [
                'Bucket'     => $this->bucket,
                'Key'        => $s3Path,
                'SourceFile' => $filePath,
            ];

            if (ENVIRONMENT === 'production') {
                $config['ACL'] = $acl;
            }

            $result = $this->s3Client->putObject($config);
    
            return $result->get('ObjectURL');
        } catch (S3Exception $ex) {
            throw new Exception('Error uploading file to S3: ' . $ex->getMessage(), 0, $ex);
        }
    }

    /**
     *  Get a file URL from S3
     *
     * @param string $s3Path
     * @return string
     */
    public function getFileUrl(string $s3Path): string
    {
        return $this->s3Client->getObjectUrl($this->bucket, $s3Path);
    }

    /**
     * Generate a pre-signed URL for an object
     *
     * @param string $s3Path
     * @param string $expires
     * @throws Exception
     * @return string
     */
    public function createPresignedRequest(
        string $s3Path,
        string $expires = '+24 hour'
    ): string {
        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $s3Path,
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, $expires);

            return (string) $request->getUri();
        } catch (AwsException $e) {
            throw new Exception('Error retrieving url: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file from S3
     *
     * @param string $s3Path
     * @throws Exception
     * @return bool
     */
    public function delete(string $s3Path): bool
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $s3Path,
            ]);
            return true;
        } catch (AwsException $e) {
            throw new Exception('Error deleting: ' . $e->getMessage());
        }
    }
}
