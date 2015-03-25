<?php

namespace Openpp\NotificationHubsRest\NotificationHub;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
interface ApiContentInterface
{
    /**
     * Returns the Content-Type.
     *
     * @return string
     */
    public function getContentType();

    /**
     * Returns the API request headers.
     *
     * @return array
     *
     */
    public function getHeaders();

    /**
     * Returns the API URI.
     *
     * @param string $endpoint
     * @param string $hubPath
     *
     * @return string
     */
    public function buildUri($endpoint, $hubPath);

    /**
     * Returns the API request payload.
     *
     * @return string
     *
     */
    public function getPayload();

    /**
     * Parses the API response and returns the response parameters.
     *
     * @param string $response
     *
     * @return array
     */
    public function scrapeResponse($response);
}
