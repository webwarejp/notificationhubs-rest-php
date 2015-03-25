<?php

namespace Openpp\NotificationHubsRest\Notification\Tests;

use Openpp\NotificationHubsRest\Notification\AppleNotification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class AppleNotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContentType()
    {
        $notification = new AppleNotification('Hello!');

        $this->assertEquals('application/json;charset=utf-8', $notification->getContentType());
    }

    public function testGetHeadersWithNoTags()
    {
        $notification = new AppleNotification('Hello!');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: apple',
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithATag()
    {
        $notification = new AppleNotification('Hello!', array(), 'female');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: apple',
            'ServiceBusNotification-Tags: female'
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithTags()
    {
        $notification = new AppleNotification('Hello!', array(), array('ios', 'female', 'japanese'));

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: apple',
            'ServiceBusNotification-Tags: ios || female || japanese'
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithTagExpression()
    {
        $notification = new AppleNotification('Hello!', array(), '(ios && female) || japanese');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: apple',
            'ServiceBusNotification-Tags: (ios && female) || japanese'
        ), $notification->getHeaders());
    }

    public function testBuildUri()
    {
        $notification = new AppleNotification('Hello!');

        $this->assertEquals(
                'aaa.servicebus.windows.net/myhub/messages/',
                $notification->buildUri('aaa.servicebus.windows.net/', 'myhub'));
    }

    public function testScrapeResponse()
    {
        $notification = new AppleNotification('Hello!');
        $notification->scrapeResponse('');
    }

    public function testGetPayloadWithNoOptionsAndNoProperies()
    {
        $notification = new AppleNotification('Hello!');
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"aps" : { "alert" : "Hello!" }}', $payload);
    }

    public function testGetPayloadWithSupportedOptionsAndNoProperies()
    {
        $notification = new AppleNotification('Hello!', array(
            'sound' => 'default',
            'badge' => 3,
            'content-available' => 1,));
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"aps" :  {"alert" : "Hello!" ,"sound" : "default", "badge" : 3, "content-available" : 1}}', $payload);
    }

    public function testGetPayloadWithUnsupportedOptionsAndNoProperies()
    {
        $notification = new AppleNotification('Hello!', array(
            'badge' => 3,
            'collapse_key' => 'aaa'
        ));
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"aps" :  {"alert" : "Hello!" , "badge" : 3}}', $payload);
    }

    public function testGetPayloadWithNoOptionsAndSupportedProperies()
    {
        $notification = new AppleNotification(array(
            'title' => 'Game Request',
            'body'  => 'Bob wants to play poker',
            'action-loc-key' => 'PLAY',
            'title-loc-key' => 'GAME',
            'title-loc-args' => array("Jenna", "Frank"),
            'loc-key' => 'PLAY_GAME',
            'loc-args' => array("poker"),
            'launch-image' => 'Default.png'
        ), array());
        $payload = $notification->getPayload();

        $expected = <<<JSON
{"aps" : {
  "alert" : {
    "title" : "Game Request",
    "body" : "Bob wants to play poker",
    "action-loc-key" : "PLAY",
    "title-loc-key": "GAME",
    "title-loc-args" : [ "Jenna", "Frank" ],
    "loc-key" : "PLAY_GAME",
    "loc-args" : ["poker"],
    "launch-image" : "Default.png"
  }
}}
JSON;

        $this->assertJsonStringEqualsJsonString($expected , $payload);
    }

    public function testGetPayloadWithNoOptionsAndUnsupportedProperies()
    {
        $notification = new AppleNotification(array(
            'alert' => 'Hello!!',
            'action-loc-key' => 'PLAY',
            'title-loc-key' => 'GAME',
            'loc-key' => 'PLAY_GAME',
            'loc-args' => array("poker"),
            'title-loc-args' => array("Jenna", "Frank"),
            'launch-image' => 'Default.png'
        ), array());
        $payload = $notification->getPayload();

        $expected = <<<JSON
{"aps" : {
  "alert" : {
    "action-loc-key" : "PLAY",
    "title-loc-key": "GAME",
    "title-loc-args" : [ "Jenna", "Frank" ],
    "loc-key" : "PLAY_GAME",
    "loc-args" : ["poker"],
    "launch-image" : "Default.png"
  }
}}
JSON;
    
        $this->assertJsonStringEqualsJsonString($expected , $payload);
    }

    public function testGetPayloadWithSupportedOptionsAndSupportedProperies()
    {
        $notification = new AppleNotification(array(
            'title' => 'Game Request',
            'body'  => 'Bob wants to play poker',
        ), array(
            'badge' => 3,
            'sound' => 'default',
            'content-available' => 1
        ));
        $payload = $notification->getPayload();

        $expected = <<<JSON
{"aps" : {
  "alert" : {
    "title" : "Game Request",
    "body" : "Bob wants to play poker"
  },
  "badge" : 3,
  "sound" : "default",
  "content-available" : 1
}}
JSON;

        $this->assertJsonStringEqualsJsonString($expected , $payload);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetPayloadWithInvalidAlert()
    {
        $notification = new AppleNotification(new \stdClass());
        $payload = $notification->getPayload();
    }
}