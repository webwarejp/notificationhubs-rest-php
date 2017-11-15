<?php

namespace Openpp\NotificationHubsRest\NotificationHub;

use Openpp\NotificationHubsRest\Notification\NotificationInterface;
use Openpp\NotificationHubsRest\Registration\GcmRegistration;
use Openpp\NotificationHubsRest\Registration\RegistrationInterface;

class NotificationHub
{
    const API_VERSION = '?api-version=2013-08';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $hubPath;

    /**
     * @var string
     */
    private $sasKeyName;

    /**
     * @var string
     */
    private $sasKeyValue;

    /**
     * Initializes a new NotificationHub.
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
     * Send a Notification.
     *
     * @param NotificationInterface $notification
     */
    public function sendNotification(NotificationInterface $notification)
    {
        $uri = $notification->buildUri($this->endpoint, $this->hubPath).self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $notification->getHeaders());

        $this->request(self::METHOD_POST, $uri, $headers, $notification->getPayload());
    }

    /**
     * Create Registration.
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

        $uri = $registration->buildUri($this->endpoint, $this->hubPath).self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $registration->getHeaders());

        $response = $this->request(self::METHOD_POST, $uri, $headers, $registration->getPayload());

        return $registration->scrapeResponse($response);
    }

    /**
     * Create or Update Registration.
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

        $uri = $registration->buildUri($this->endpoint, $this->hubPath).self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $registration->getHeaders());

        $response = $this->request(self::METHOD_PUT, $uri, $headers, $registration->getPayload());

        return $registration->scrapeResponse($response);
    }

    /**
     * Delete Registration.
     *
     * @param ApiContentInterface $registration
     *
     * @throws \RuntimeException
     */
    public function deleteRegistration(RegistrationInterface $registration)
    {
        if (!$registration->getRegistrationId() || !$registration->getETag()) {
            throw new \RuntimeException('Registration ID and ETag are mandatory.');
        }

        $uri = $registration->buildUri($this->endpoint, $this->hubPath).self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $registration->getHeaders());

        $this->request(self::METHOD_DELETE, $uri, $headers);
    }

    /**
     * Read Registration.
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

        $uri = $registration->buildUri($this->endpoint, $this->hubPath).self::API_VERSION;

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $registration->getHeaders());

        $response = $this->request(self::METHOD_GET, $uri, $headers);

        return $registration->scrapeResponse($response);
    }

    /**
     * Read All Registrations of a Channel.
     *
     * @param RegistrationInterface $registration
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function readAllRegistrationsOfAChannel(RegistrationInterface $registration)
    {
        if (!$registration->getToken()) {
            throw new \RuntimeException('Token is mandatory.');
        }

        $uri = $registration->buildUri($this->endpoint, $this->hubPath).self::API_VERSION.
                '&$filter='.urlencode($registration->getTokenTag().' eq \''.$registration->getToken().'\'');

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $registration->getHeaders());

        $response = $this->request(self::METHOD_GET, $uri, $headers);

        $dom = new \DOMDocument();
        $dom->loadXML($response);

        $registrations = [];
        foreach ($dom->getElementsByTagName('entry') as $entry) {
            $registrations[] = $registration->scrapeResponse($dom->saveXML($entry));
        }

        return $registrations;
    }

    /**
     * Create Registration ID.
     *
     * @return string Registration ID
     */
    public function createRegistrationId()
    {
        $registration = new GcmRegistration();
        // build uri
        $uri = $this->endpoint.$this->hubPath.'/registrationIDs/';

        $token = $this->generateSasToken($uri);
        $headers = array_merge(['Authorization: '.$token], $registration->getHeaders());
        $headers = array_merge(['Content-length: 0'], $headers);

        $response = $this->request(self::METHOD_POST, $uri.self::API_VERSION, $headers, null, true);

        preg_match(
            '#'.$uri.'([^?]+)'.preg_quote(self::API_VERSION).'#',
            $response,
            $matches
        );

        return $matches[1];
    }

    /**
     * Send the request to API.
     *
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param string $payload
     * @param bool   $responseHeader
     *
     * @throws \RuntimeException
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    protected function request($method, $uri, $headers, $payload = null, $responseHeader = false)
    {
        $ch = curl_init($uri);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => $responseHeader,
            CURLOPT_HTTPHEADER => $headers,
        ];

        $options[CURLOPT_CUSTOMREQUEST] = $method;

        if (!is_null($payload)) {
            $options[CURLOPT_POSTFIELDS] = $payload;
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if (false === $response) {
            throw new \RuntimeException(curl_error($ch));
        }

        $info = curl_getinfo($ch);
        if (200 != $info['http_code'] && 201 != $info['http_code']) {
            throw new \RuntimeException('Error sending request: '.$info['http_code'].' msg: '.$response);
        }

        return $response;
    }

    /**
     * Parses the connection string.
     *
     * @param string $connectionString
     *
     * @throws \RuntimeException
     */
    private function parseConnectionString($connectionString)
    {
        $parts = explode(';', $connectionString);
        if (3 != count($parts)) {
            throw new \RuntimeException('Error parsing connection string: '.$connectionString);
        }

        foreach ($parts as $part) {
            if (0 === strpos($part, 'Endpoint')) {
                $this->endpoint = 'https'.substr($part, 11);
            } elseif (0 === strpos($part, 'SharedAccessKeyName')) {
                $this->sasKeyName = substr($part, 20);
            } elseif (0 === strpos($part, 'SharedAccessKey')) {
                $this->sasKeyValue = substr($part, 16);
            }
        }

        if (!$this->endpoint || !$this->sasKeyName || !$this->sasKeyValue) {
            throw new \RuntimeException('Invalid connection string: '.$connectionString);
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
        $toSign = $targetUri."\n".$expires;
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $this->sasKeyValue, true)));
        $token = 'SharedAccessSignature sr='.$targetUri.'&sig='.$signature.'&se='.$expires.'&skn='.$this->sasKeyName;

        return $token;
    }
}
