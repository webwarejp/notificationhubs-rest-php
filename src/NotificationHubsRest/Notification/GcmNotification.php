<?php

namespace Openpp\NotificationHubsRest\Notification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class GcmNotification extends AbstractNotification
{
    private $supportedOptions = array(
        'collapse_key',
        'delay_while_idle',
        'time_to_live',
        'restricted_package_name',
        'dry_run',
    );

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return "gcm";
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
        if ($this->options) {
            $payload = array_intersect_key($this->options, array_fill_keys($this->supportedOptions, 0));
        } else {
            $payload = array();
        }

        if (is_array($this->alert)) {
            $payload += array('data' => $this->alert);
        } else if (is_scalar($this->alert)) {
            $payload += array('data' => array('msg' => $this->alert));
        } else {
            throw new \RuntimeException('Invalid alert.');
        }

        return json_encode($payload);
    }
}