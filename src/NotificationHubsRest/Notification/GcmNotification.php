<?php

namespace Openpp\NotificationHubsRest\Notification;

class GcmNotification extends AbstractNotification
{
    /**
     * @var string[]
     */
    private $supportedOptions = [
        'collapse_key',
        'delay_while_idle',
        'time_to_live',
        'restricted_package_name',
        'dry_run',
    ];

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'gcm';
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'application/json;charset=utf-8';
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        if (!empty($this->options)) {
            $payload = array_intersect_key($this->options, array_fill_keys($this->supportedOptions, 0));
        } else {
            $payload = [];
        }

        if (is_array($this->alert)) {
            $payload += ['data' => $this->alert];
        } elseif (is_scalar($this->alert)) {
            $payload += ['data' => ['message' => $this->alert]];
        } else {
            throw new \RuntimeException('Invalid alert.');
        }

        return json_encode($payload);
    }
}
