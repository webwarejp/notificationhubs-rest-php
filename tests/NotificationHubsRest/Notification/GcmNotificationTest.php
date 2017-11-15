<?php

namespace Openpp\NotificationHubsRest\Notification\Tests;

use Openpp\NotificationHubsRest\Notification\GcmNotification;

class GcmNotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContentType()
    {
        $notification = new GcmNotification('Hello!');

        $this->assertEquals('application/json;charset=utf-8', $notification->getContentType());
    }

    public function testGetHeadersWithNoTags()
    {
        $notification = new GcmNotification('Hello!');

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithATag()
    {
        $notification = new GcmNotification('Hello!', [], 'male');

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-Tags: male',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithTags()
    {
        $notification = new GcmNotification('Hello!', [], ['android', 'male', 'japanese']);

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-Tags: android || male || japanese',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithTagExpression()
    {
        $notification = new GcmNotification('Hello!', [], '(android && male) || japanese');

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-Tags: (android && male) || japanese',
        ], $notification->getHeaders());
    }

    public function testGetHeadersWithScheduleTime()
    {
        $scheduleTime = new \DateTime();
        $notification = new GcmNotification('Hello!', [], '', $scheduleTime);

        $scheduleTime->setTimeZone(new \DateTimeZone('UTC'));

        $this->assertEquals([
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-ScheduleTime: '.$scheduleTime->format(GcmNotification::SCHEDULE_TIME_FORMAT),
        ], $notification->getHeaders());
    }

    public function testBuildUri()
    {
        $notification = new GcmNotification('Hello!');

        $this->assertEquals(
                'aaa.servicebus.windows.net/myhub/messages/',
                $notification->buildUri('aaa.servicebus.windows.net/', 'myhub'));
    }

    public function testGetPayloadWithNoOptionsAndNoProperies()
    {
        $notification = new GcmNotification('Hello!');
        $payload = $notification->getPayload();

        $expected = <<<'JSON'
{
  "data" : {
    "message" : "Hello!"
  }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected, $payload);
    }

    public function testGetPayloadWithSupportedOptions()
    {
        $notification = new GcmNotification('Hello!', [
            'collapse_key' => 'demo',
            'delay_while_idle' => true,
            'time_to_live' => 3,
            'restricted_package_name' => 'abc',
            'dry_run' => true, ]);
        $payload = $notification->getPayload();

        $expected = <<<'JSON'
{
  "collapse_key" : "demo",
  "delay_while_idle" : true,
  "time_to_live" : 3,
  "restricted_package_name" : "abc",
  "dry_run" : true,
  "data" : {
    "message" : "Hello!"
  }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected, $payload);
    }

    public function testGetPayloadWithUnsupportedOptionsAndNoProperies()
    {
        $notification = new GcmNotification('Hello!', [
            'badge' => 3,
            'collapse_key' => 'aaa',
        ]);
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"collapse_key" : "aaa", "data" :  {"message" : "Hello!"}}', $payload);
    }

    public function testGetPayloadWithNoOptionsAndProperies()
    {
        $notification = new GcmNotification([
            'title' => 'Game Request',
            'body' => 'Bob wants to play poker',
        ], []);
        $payload = $notification->getPayload();

        $expected = <<<'JSON'
{
  "data" : {
    "title" : "Game Request",
    "body" : "Bob wants to play poker"
  }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected, $payload);
    }

    public function testGetPayloadWithSupportedOptionsAndProperies()
    {
        $notification = new GcmNotification([
            'title' => 'Game Request',
            'body' => 'Bob wants to play poker',
        ], [
            'collapse_key' => 'demo',
            'delay_while_idle' => true,
            'time_to_live' => 3,
        ]);
        $payload = $notification->getPayload();

        $expected = <<<'JSON'
{
  "data" : {
    "title" : "Game Request",
    "body" : "Bob wants to play poker"
  },
  "collapse_key" : "demo",
  "delay_while_idle" : true,
  "time_to_live" : 3
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected, $payload);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetPayloadWithInvalidAlert()
    {
        $notification = new GcmNotification(new \stdClass());
        $payload = $notification->getPayload();
    }
}
