<?php

namespace Openpp\NotificationHubsRest\Notification\Tests;

use Openpp\NotificationHubsRest\Notification\TemplateNotification;

class TemplateNotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContentType()
    {
        $notification = new TemplateNotification(['message' => 'Hello!']);

        $this->assertEquals('application/json;charset=utf-8', $notification->getContentType());
    }

    public function testGetHeadersWithNoTags()
    {
        $notification = new TemplateNotification(['message' => 'Hello!']);

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithATag()
    {
        $notification = new TemplateNotification(['message' => 'Hello!'], [], 'female');

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
            'ServiceBusNotification-Tags: female',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithTags()
    {
        $notification = new TemplateNotification(['message' => 'Hello!'], [], ['ios', 'female', 'japanese']);

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
            'ServiceBusNotification-Tags: ios || female || japanese',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithTagExpression()
    {
        $notification = new TemplateNotification(['message' => 'Hello!'], [], '(ios && female) || japanese');

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
            'ServiceBusNotification-Tags: (ios && female) || japanese',
        ], $notification->getHeaders());
    }

    public function testBuildUri()
    {
        $notification = new TemplateNotification(['message' => 'Hello!']);

        $this->assertEquals(
                'aaa.servicebus.windows.net/myhub/messages/',
                $notification->buildUri('aaa.servicebus.windows.net/', 'myhub'));
    }

    public function testGetPayloadWithArray()
    {
        $notification = new TemplateNotification(['message' => 'Hello!', 'name' => 'John']);
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"message" : "Hello!", "name" : "John" }', $payload);
    }

    public function testGetPayloadWithScalar()
    {
        $notification = new TemplateNotification('{"message" : "Hello!", "name" : "John" }');
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"message" : "Hello!", "name" : "John" }', $payload);
    }
}
