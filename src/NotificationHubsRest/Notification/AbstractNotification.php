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
    protected $scheduleTime;
    protected $deviceToken;

    const SCHEDULE_TIME_FORMAT = 'Y-m-d\TH:i:s';

    /**
     * Constructor
     *
     * @param string|array $alert
     * @param array        $options
     * @param string|array $tagsOrTagExpression
     * @param \DateTime    $scheduleTime
     *
     * @throws \RuntimeException
     */
    public function __construct($alert, $options = array(), $tagsOrTagExpression = '', \DateTime $scheduleTime = null)
    {
        $this->alert = $alert;
        $this->options = $options;
        $this->tagsOrTagExpression = $tagsOrTagExpression;
        $this->scheduleTime = $scheduleTime;
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

        if ($this->scheduleTime instanceof \DateTime) {
            $this->scheduleTime->setTimeZone(new \DateTimeZone('UTC'));
            $headers[] = 'ServiceBusNotification-ScheduleTime: ' . $this->scheduleTime->format(self::SCHEDULE_TIME_FORMAT);
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUri($endpoint, $hubPath)
    {
        if ($this->scheduleTime instanceof \DateTime) {
            return $endpoint . $hubPath . '/schedulednotifications/';
        }
        return $endpoint . $hubPath . '/messages/';
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($deviceToken){
        $this->deviceToken = $deviceToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(){
        return $this->deviceToken;
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
