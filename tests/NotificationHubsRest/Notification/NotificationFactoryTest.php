<?php

namespace Openpp\NotificationHubsRest\Notification\Tests;

use Openpp\NotificationHubsRest\Notification\NotificationFactory;
use Openpp\NotificationHubsRest\Notification\GcmNotification;
use Openpp\NotificationHubsRest\Notification\AppleNotification;
use Openpp\NotificationHubsRest\Notification\TemplateNotification;

class NotificationBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $this->factory = new NotificationFactory();
        parent::setUp();
    }

    public function testCreateGcmNotification()
    {
        $notification = $this->factory->createNotification('gcm', 'Hello!');
        $this->assertTrue($notification instanceof GcmNotification);
    }

    public function testCreateAppleRegistration()
    {
        $notification = $this->factory->createNotification('apple', 'Hello!');
        $this->assertTrue($notification instanceof AppleNotification);
    }

    public function testCreateTemplateRegistration()
    {
        $notification = $this->factory->createNotification('template', ['message' => 'Hello!']);
        $this->assertTrue($notification instanceof TemplateNotification);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateInvalidNotification()
    {
        $this->factory->createNotification('windows', 'Hello!');
    }
}
