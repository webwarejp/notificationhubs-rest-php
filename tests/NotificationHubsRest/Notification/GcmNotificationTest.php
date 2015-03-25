<?php

namespace Openpp\NotificationHubsRest\Notification\Tests;

use Openpp\NotificationHubsRest\Notification\GcmNotification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
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

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithATag()
    {
        $notification = new GcmNotification('Hello!', array(), 'male');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-Tags: male'
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithTags()
    {
        $notification = new GcmNotification('Hello!', array(), array('android', 'male', 'japanese'));

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-Tags: android || male || japanese'
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithTagExpression()
    {
        $notification = new GcmNotification('Hello!', array(), '(android && male) || japanese');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: gcm',
            'ServiceBusNotification-Tags: (android && male) || japanese'
        ), $notification->getHeaders());
    }

    public function testBuildUri()
    {
        $notification = new GcmNotification('Hello!');

        $this->assertEquals(
                'aaa.servicebus.windows.net/myhub/messages/',
                $notification->buildUri('aaa.servicebus.windows.net/', 'myhub'));
    }

    public function testScrapeResponse()
    {
        $notification = new GcmNotification('Hello!');
        $notification->scrapeResponse('');
    }

    public function testGetPayloadWithNoOptionsAndNoProperies()
    {
        $notification = new GcmNotification('Hello!');
        $payload = $notification->getPayload();

        $expected = <<<JSON
{
  "data" : {
    "msg" : "Hello!"
  }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected, $payload);
    }

    public function testGetPayloadWithSupportedOptions()
    {
        $notification = new GcmNotification('Hello!', array(
            'collapse_key' => "demo",
            'delay_while_idle' => true,
            'time_to_live' => 3,
            'restricted_package_name' => 'abc',
            'dry_run' => true,));
        $payload = $notification->getPayload();

        $expected = <<<JSON
{
  "collapse_key" : "demo",
  "delay_while_idle" : true,
  "time_to_live" : 3,
  "restricted_package_name" : "abc",
  "dry_run" : true,
  "data" : {
    "msg" : "Hello!"
  }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected, $payload);
    }

    public function testGetPayloadWithUnsupportedOptionsAndNoProperies()
    {
        $notification = new GcmNotification('Hello!', array(
            'badge' => 3,
            'collapse_key' => 'aaa'
        ));
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"collapse_key" : "aaa", "data" :  {"msg" : "Hello!"}}', $payload);
    }

    public function testGetPayloadWithNoOptionsAndProperies()
    {
        $notification = new GcmNotification(array(
            'title' => 'Game Request',
            'body'  => 'Bob wants to play poker',
        ), array());
        $payload = $notification->getPayload();

        $expected = <<<JSON
{
  "data" : {
    "title" : "Game Request",
    "body" : "Bob wants to play poker"
  }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expected , $payload);
    }


    public function testGetPayloadWithSupportedOptionsAndProperies()
    {
        $notification = new GcmNotification(array(
            'title' => 'Game Request',
            'body'  => 'Bob wants to play poker',
        ), array(
            'collapse_key' => "demo",
            'delay_while_idle' => true,
            'time_to_live' => 3,
        ));
        $payload = $notification->getPayload();

        $expected = <<<JSON
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

        $this->assertJsonStringEqualsJsonString($expected , $payload);
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