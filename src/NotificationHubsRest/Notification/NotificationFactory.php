<?php

namespace Openpp\NotificationHubsRest\Notification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class NotificationFactory
{
    /**
     * Creates the Notification class according to the format.
     *
     * @param string       $format               "gcm", "apple", "template"
     * @param string|array $alert                "data" for gcm, "alert" for apple, payload for template
     * @param array        $options              message options
     * @param string|array $tagsOrTagExpression  a tag or tags array or tag expression
     * @param \DateTime    $scheduleTime         the date to deliver the notification at
     *
     * @throws \RuntimeException
     *
     * @return NotificationInterface
     */
    public function createNotification($format, $alert, array $options = array(), $tagsOrTagExpression = '', \DateTime $scheduleTime = null)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($format) . 'Notification';

        if (!class_exists($class)) {
            throw new \RuntimeException('Invalid format: ' . $format);
        }

        return new $class($alert, $options, $tagsOrTagExpression, $scheduleTime);
    }
}