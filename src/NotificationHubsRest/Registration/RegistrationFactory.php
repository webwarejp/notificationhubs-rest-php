<?php

namespace Openpp\NotificationHubsRest\Registration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class RegistrationFactory
{
    /**
     * Creates the Registration class according to the type.
     *
     * @param string $type  "gcm", "apple"
     *
     * @throws \RuntimeException
     *
     * @return RegistrationInterface
     */
    public function createRegistration($type)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($type) . 'Registration';

        if (!class_exists($class)) {
            throw new \RuntimeException('Invalid type: ' . $type);
        }

        return new $class;
    }
}