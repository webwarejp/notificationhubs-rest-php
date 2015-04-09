<?php

namespace Openpp\NotificationHubsRest\Notification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class TemplateNotification extends AbstractNotification
{
    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return "template";
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
        if (is_array($this->alert)) {
            $payload = json_encode($this->alert);
        } else if (is_scalar($this->alert)) {
            $payload = $this->alert;
        } else {
            throw new \RuntimeException('Invalid alert.');
        }

        return $payload;
    }
}
