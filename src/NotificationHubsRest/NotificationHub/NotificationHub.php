<?php

namespace Openpp\NotificationHubsRest\NotificationHub;

use Openpp\NotificationHubsRest\Notification\NotificationInterface;
use Openpp\NotificationHubsRest\Registration\RegistrationInterface;
use Openpp\NotificationHubsRest\Registration\GcmRegistration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class NotificationHub
{
    const API_VERSION = "?api-version=2013-08";

    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    private $endpoint;
    private $hubPath;
    private $sasKeyName;
    private $sasKeyValue;

    /**
     * Constructor
     *
     * @param string $connectionString
     * @param string $hubPath
     */
    public function __construct($connectionString, $hubPath)
    {
        $this->hubPath = $hubPath;
        $this->parseConnectionString($connectionString);
    }

    /**
     * Parses the connection string.
     *
     * @param string $connectionString
     *
     * @throws RuntimeException
     */
    private function parseConnectionString($connectionString)
    {
        $parts = explode(";", $connectionString);
        if (sizeof($parts) != 3) {
            throw new \RuntimeException("Error parsing connection string: " . $connectionString);
        }

        foreach ($parts as $part) {
            if (strpos($part, "Endpoint") === 0) {
                $this->endpoint = "https" . substr($part, 11);
            } else if (strpos($part, "SharedAccessKeyName") === 0) {
                $this->sasKeyName = substr($part, 20);
            } else if (strpos($part, "SharedAccessKey") === 0) {
                $this->sasKeyValue = substr($part, 16);
            }
        }

        if (!$this->endpoint || !$this->sasKeyName || !$this->sasKeyValue) {
            throw new \RuntimeException("Invalid connection string: " . $connectionString);
        }
    }

    /**
     * Generates the SAS token.
     *
     * @param string $uri
     *
     * @return string
     */
    private function generateSasToken($uri)
    {
        $targetUri = strtolower(rawurlencode(strtolower($uri)));
        $expires = time();
        $expiresInMins = 60;
        $expires = $expires + $expiresInMins * 60;
        $toSign = $targetUri . "\n" . $expires;
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $this->sasKeyValue, TRUE)));
        $token = "SharedAccessSignature sr=" . $targetUri . "&sig=" . $signature . "&se=" . $expires . "&skn=" . $this->sasKeyName;

        return $token;
    }

    /**
     * Send a Notification
     *
     * @param NotificationInterface $notification
     *
     * @return void
     */
    public function sendNotification(NotificationInterface $notification)
    {
        $uri = $notification->buildUri($this->endpoint, $this->hubPath) . self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(array('Authorization: ' . $token), $notification->getHeaders());

        $this->request(self::METHOD_POST, $uri, $headers, $notification->getPayload());
    }

    /**
     * Create Registration
     *
     * @param RegistrationInterface $registration
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function createRegistration(RegistrationInterface $registration)
    {
        if ($registration->getRegistrationId() || $registration->getETag()) {
            throw new \RuntimeException('Registration ID and ETag must be empty.');
        }

        $uri = $registration->buildUri($this->endpoint, $this->hubPath) . self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(array('Authorization: ' . $token), $registration->getHeaders());

        $response = $this->request(self::METHOD_POST, $uri, $headers, $registration->getPayload());

        return $registration->scrapeResponse($response);
    }

    /**
     * Create or Update Registration
     *
     * @param RegistrationInterface $registration
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function updateRegistration(RegistrationInterface $registration)
    {
        if (!$registration->getRegistrationId()) {
            throw new \RuntimeException('Registration ID is mandatory.');
        }

        $uri = $registration->buildUri($this->endpoint, $this->hubPath) . self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(array('Authorization: ' . $token), $registration->getHeaders());

        $response = $this->request(self::METHOD_PUT, $uri, $headers, $registration->getPayload());

        return $registration->scrapeResponse($response);
    }

    /**
     * Delete Registration
     *
     * @param ApiContentInterface $registration
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function deleteRegistration(RegistrationInterface $registration)
    {
        if (!$registration->getRegistrationId() || !$registration->getETag()) {
            throw new \RuntimeException('Registration ID and ETag are mandatory.');
        }

        $uri = $registration->buildUri($this->endpoint, $this->hubPath) . self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(array('Authorization: ' . $token), $registration->getHeaders());

        $this->request(self::METHOD_DELETE, $uri, $headers);
    }

    /**
     * Read Registration
     *
     * @param RegistrationInterface $registration
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function readRegistration(RegistrationInterface $registration)
    {
        if (!$registration->getRegistrationId()) {
            throw new \RuntimeException('Registration ID is mandatory.');
        }

        $uri = $registration->buildUri($this->endpoint, $this->hubPath) . self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(array('Authorization: ' . $token), $registration->getHeaders());

        $response = $this->request(self::METHOD_GET, $uri, $headers);

        return $registration->scrapeResponse($response);
    }

    /**
     * Create Registration ID
     *
     * @return string Registration ID
     */
    public function createRegistrationId()
    {
        $registration = new GcmRegistration();
        // build uri
        $uri = $this->endpoint . $this->hubPath . "/registrationIDs/";

        $token = $this->generateSasToken($uri);
        $headers = array_merge(array('Authorization: ' . $token), $registration->getHeaders());
        $headers = array_merge(array('Content-length: 0'), $headers);

        $response = $this->request(self::METHOD_POST, $uri . self::API_VERSION, $headers, null, true);

        preg_match(
            '#' . $uri . '([^?]+)' . preg_quote(self::API_VERSION) . '#',
            $response,
            $matches
        );

        return $matches[1];
    }

    /**
     * Send the request to API.
     *
     * @param string  $method
     * @param string  $uri
     * @param array   $headers
     * @param string  $payload
     * @param boolean $responseHeader
     *
     * @throws \RuntimeException
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    protected function request($method, $uri, $headers, $payload = null, $responseHeader = false)
    {
        $ch = curl_init($uri);

        $options = array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HEADER => $responseHeader,
            CURLOPT_HTTPHEADER => $headers,
        );

        $options[CURLOPT_CUSTOMREQUEST] = $method;

        if (!is_null($payload)) {
            $options[CURLOPT_POSTFIELDS] = $payload;
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if ($response === FALSE) {
            throw new \RuntimeException(curl_error($ch));
        }

        $info = curl_getinfo($ch);
        if ($info['http_code'] != 200 && $info['http_code'] != 201) {
            throw new \RuntimeException('Error sending notificaiton: ' . $info['http_code'] . ' msg: ' . $response);
        }

        return $response;
    }
}
