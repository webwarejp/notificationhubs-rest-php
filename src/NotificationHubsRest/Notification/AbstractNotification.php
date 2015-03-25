<?php

namespace Openpp\NotificationHubsRest\Notification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
abstract class AbstractNotification implements NotificationInterface
{
    protected $alert;
    protected $options;
    protected $tagsOrTagExpression;

    /**
     * Constructor
     *
     * @param string|array $alert
     * @param array        $options
     * @param string|array $tagsOrTagExpression
     *
     * @throws \RuntimeException
     */
    public function __construct($alert, $options = array(), $tagsOrTagExpression = '')
    {
        $this->alert = $alert;
        $this->options = $options;
        $this->tagsOrTagExpression = $tagsOrTagExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        if (!$this->tagsOrTagExpression) {
            $tagExpression = '';
        } else if (is_array($this->tagsOrTagExpression)) {
            $tagExpression = implode(' || ', $this->tagsOrTagExpression);
        } else {
            $tagExpression = $this->tagsOrTagExpression;
        }

        $headers = array(
            'Content-Type: ' . $this->getContentType(),
            'ServiceBusNotification-Format: ' . $this->getFormat()
        );

        if ('' !== $tagExpression) {
            $headers[] = 'ServiceBusNotification-Tags: ' . $tagExpression;
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUri($endpoint, $hubPath)
    {
        return $endpoint . $hubPath . '/messages/';
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function scrapeResponse($response)
    {
    }
}
