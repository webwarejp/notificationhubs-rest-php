<?php

namespace Openpp\NotificationHubsRest\Notification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class AppleNotification extends AbstractNotification
{
    private $supportedOptions = array(
        'badge',
        'sound',
        'content-available',
    );

    private $supportedAlertProperties = array(
        'title',
        'body',
        'title-loc-key',
        'title-loc-args',
        'action-loc-key',
        'loc-key',
        'loc-args',
        'launch-image',
    );

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return "apple";
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return "application/json;charset=utf-8";
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        if (!empty($this->options)) {
            $payload = array_intersect_key($this->options, array_fill_keys($this->supportedOptions, 0));
        } else {
            $payload = array();
        }

        if (is_array($this->alert)) {
            $alert = array_intersect_key($this->alert, array_fill_keys($this->supportedAlertProperties, 0));
            $payload += array('alert' => $alert);
        } else if (is_scalar($this->alert)) {
            $payload += array('alert' => $this->alert);
        } else {
            throw new \RuntimeException('Invalid alert.');
        }

        $payload = array('aps' => $payload);

        return json_encode($payload);
    }
}
