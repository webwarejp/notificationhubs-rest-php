<?php

namespace Openpp\NotificationHubsRest\Registration\Tests;

use Openpp\NotificationHubsRest\Registration\RegistrationFactory;
use Openpp\NotificationHubsRest\Registration\GcmRegistration;
use Openpp\NotificationHubsRest\Registration\AppleRegistration;

/**
 *
 * @author shiroko@webware.co.jp
 *
 */
class RegistrationFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $this->factory = new RegistrationFactory();
        parent::setUp();
    }

    public function testCreateGcmRegistration()
    {
        $registration = $this->factory->createRegistration("gcm");
        $this->assertTrue($registration instanceof GcmRegistration);
    }

    public function testCreateAppleRegistration()
    {
        $registration = $this->factory->createRegistration("apple");
        $this->assertTrue($registration instanceof AppleRegistration);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateInvalidRegistration()
    {
        $registration = $this->factory->createRegistration("windows");
    }
}