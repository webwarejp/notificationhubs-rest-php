<?php

namespace Openpp\NotificationHubsRest\Notification;

class TemplateNotification extends AbstractNotification
{
    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'template';
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
        if (is_array($this->alert)) {
            $payload = json_encode($this->alert);
        } elseif (is_string($this->alert)) {
            $payload = $this->alert;
        } else {
            throw new \RuntimeException('Invalid alert.');
        }

        return $payload;
    }
}
