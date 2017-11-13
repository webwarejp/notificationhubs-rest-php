<?php

namespace Openpp\NotificationHubsRest\Notification;

abstract class AbstractNotification implements NotificationInterface
{
    const SCHEDULE_TIME_FORMAT = 'Y-m-d\TH:i:s';

    /**
     * @var tring|array
     */
    protected $alert;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string|string[]
     */
    protected $tagsOrTagExpression;

    /**
     * @var \DateTime
     */
    protected $scheduleTime;

    /**
     * Initializes a new Notification.
     *
     * @param string|string[] $alert
     * @param array           $options
     * @param string|string[] $tagsOrTagExpression
     * @param \DateTime       $scheduleTime
     *
     * @throws \RuntimeException
     */
    public function __construct($alert, $options = [], $tagsOrTagExpression = '', \DateTime $scheduleTime = null)
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
        } elseif (is_array($this->tagsOrTagExpression)) {
            $tagExpression = implode(' || ', $this->tagsOrTagExpression);
        } else {
            $tagExpression = $this->tagsOrTagExpression;
        }

        $headers = [
            'Content-Type: '.$this->getContentType(),
            'ServiceBusNotification-Format: '.$this->getFormat(),
        ];

        if ('' !== $tagExpression) {
            $headers[] = 'ServiceBusNotification-Tags: '.$tagExpression;
        }

        if ($this->scheduleTime instanceof \DateTime) {
            $this->scheduleTime->setTimeZone(new \DateTimeZone('UTC'));
            $headers[] = 'ServiceBusNotification-ScheduleTime: '.$this->scheduleTime->format(self::SCHEDULE_TIME_FORMAT);
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUri($endpoint, $hubPath)
    {
        if ($this->scheduleTime instanceof \DateTime) {
            return $endpoint.$hubPath.'/schedulednotifications/';
        }

        return $endpoint.$hubPath.'/messages/';
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
