<?php

namespace Openpp\NotificationHubsRest\Notification;

use Openpp\NotificationHubsRest\NotificationHub\ApiContentInterface;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
interface NotificationInterface extends ApiContentInterface
{
    /**
     * Returns the ServiceBusNotification-Format.
     *
     * @return string
     */
    public function getFormat();
}