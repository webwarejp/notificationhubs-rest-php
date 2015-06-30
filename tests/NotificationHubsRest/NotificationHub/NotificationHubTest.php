<?php

namespace Openpp\NotificationHubsRest\NotificationHub\Tests;

use Openpp\NotificationHubsRest\Notification\GcmNotification;
use Openpp\NotificationHubsRest\Registration\GcmRegistration;
use Openpp\NotificationHubsRest\NotificationHub\NotificationHub;

class NotificationHubTest extends \PHPUnit_Framework_TestCase
{
    const CONNECTION_STRING = 'Endpoint=sb://buildhub-ns.servicebus.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=bHIqy5zmG3OrsMWC6Iy/9bXJOSuTFW2uk7H21S6ipow=';
    protected $mock;

    public function setUp()
    {
        parent::setUp();
        $this->mock = $this->getMockBuilder('Openpp\NotificationHubsRest\NotificationHub\NotificationHub')
                     ->setConstructorArgs(array(self::CONNECTION_STRING, 'myHub'))
                     ->setMethods(array('request'))
                     ->getMock();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidPartsConnectionString()
    {
        new NotificationHub('abcde;fghijk', 'myHub');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoEndpointConnectionString()
    {
        new NotificationHub('SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=bHIqy5zmG3OrsMWC6Iy/9bXJOSuTFW2uk7H21S6ipow=', 'myHub');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function NoSharedAccessKeyNameConnectionString()
    {
        new NotificationHub('Endpoint=sb://buildhub-ns.servicebus.windows.net/;SharedAccessKey=bHIqy5zmG3OrsMWC6Iy/9bXJOSuTFW2uk7H21S6ipow=', 'myHub');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoSharedAccessKeyConnectionString()
    {
        new NotificationHub('Endpoint=sb://buildhub-ns.servicebus.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;', 'myHub');
    }

    public function testSendNotification()
    {
        $this->mock->expects($this->once())
             ->method('request')
             ->with($this->equalTo('POST'),
                    $this->equalTo('https://buildhub-ns.servicebus.windows.net/myHub/messages/?api-version=2013-08'),
                    $this->callback(function ($headers) {
                        $content = preg_grep('#^Content-Type: application/json;charset=utf-8$#', $headers);
                        $format = preg_grep('#^ServiceBusNotification-Format: gcm$#', $headers);
                        $auth = preg_grep('#^Authorization#', $headers);
                        if (!$content || !$format || !$auth) {
                            return false;
                        }
                        if (array_diff($headers, $content + $auth + $format)) {
                            return false;
                        }
                        return true;
                    }),
                    $this->equalTo('{"data":{"message":"Hello!"}}'),
                    $this->equalTo(false)
              )
              ->will($this->returnValue(null));

        $notification = new GcmNotification('Hello!');
        $this->mock->sendNotification($notification);
    }

    public function testCreateRegistration()
    {
        $this->mock->expects($this->once())
        ->method('request')
        ->with($this->equalTo('POST'),
                $this->equalTo('https://buildhub-ns.servicebus.windows.net/myHub/registrations/?api-version=2013-08'),
                $this->callback(function ($headers) {
                    $content = preg_grep('#^Content-Type: application/atom\+xml;type=entry;charset=utf-8$#', $headers);
                    $version = preg_grep('#^x-ms-version: 2013-08$#', $headers);
                    $auth = preg_grep('#^Authorization#', $headers);
                    if (!$content || !$version || !$auth) {
                        return false;
                    }
                    if (array_diff($headers, $content + $auth + $version)) {
                        return false;
                    }
                    return true;
                }),
                $this->anything(),
                $this->equalTo(false)
        )
        ->will($this->returnValue('
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>android, male, japanese</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId>
        </GcmRegistrationDescription>
    </content>
</entry>'));

        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('android', 'male', 'japanese'));

        $result = $this->mock->createRegistration($registration);

        $this->assertEquals(array(
            'ETag' => '3',
            'ExpirationTime' => '2014-09-01T15:57:46.778Z',
            'RegistrationId' => '2372532420827572008-85883004107185159-4',
            'Tags' => 'android, male, japanese',
            'GcmRegistrationId' => 'abcdefghijklmnopqrstuvwxyz',
        ), $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateRegistrationWithRegistrationId()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setRegistrationId('2372532420827572008-85883004107185159-4');

        $this->mock->createRegistration($registration);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateRegistrationWithEtag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setEtag('3');

        $this->mock->createRegistration($registration);
    }

    public function testUpdateRegistration()
    {
        $this->mock->expects($this->once())
        ->method('request')
        ->with($this->equalTo('PUT'),
                $this->equalTo('https://buildhub-ns.servicebus.windows.net/myHub/registrations/2372532420827572008-85883004107185159-4?api-version=2013-08'),
                $this->callback(function ($headers) {
                    $content = preg_grep('#^Content-Type: application/atom\+xml;type=entry;charset=utf-8$#', $headers);
                    $version = preg_grep('#^x-ms-version: 2013-08$#', $headers);
                    $auth = preg_grep('#^Authorization#', $headers);
                    $etag = preg_grep('#^If-Match:#', $headers);
                    if (!$content || !$version || !$auth || !$etag) {
                        return false;
                    }
                    if (array_diff($headers, $content + $auth + $version + $etag)) {
                        return false;
                    }
                    return true;
                }),
                $this->anything(),
                $this->equalTo(false)
        )
        ->will($this->returnValue('
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>4</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>android, male, japanese</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId>
        </GcmRegistrationDescription>
    </content>
</entry>'));

        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz');
        $registration->setRegistrationId('2372532420827572008-85883004107185159-4');
        $registration->setEtag('3');
        $result = $this->mock->updateRegistration($registration);

        $this->assertEquals(array(
            'ETag' => '4',
            'ExpirationTime' => '2014-09-01T15:57:46.778Z',
            'RegistrationId' => '2372532420827572008-85883004107185159-4',
            'Tags' => 'android, male, japanese',
            'GcmRegistrationId' => 'abcdefghijklmnopqrstuvwxyz',
        ), $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateRegistrationWithNoRegistrationId()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setEtag('3');

        $this->mock->updateRegistration($registration);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateRegistrationWithEtag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setRegistrationId('2372532420827572008-85883004107185159-4');

        $this->mock->updateRegistration($registration);
    }

    public function testDeleteRegistration()
    {
        $this->mock->expects($this->once())
        ->method('request')
        ->with($this->equalTo('DELETE'),
                $this->equalTo('https://buildhub-ns.servicebus.windows.net/myHub/registrations/2372532420827572008-85883004107185159-4?api-version=2013-08'),
                $this->callback(function ($headers) {
                    $content = preg_grep('#^Content-Type: application/atom\+xml;type=entry;charset=utf-8$#', $headers);
                    $version = preg_grep('#^x-ms-version: 2013-08$#', $headers);
                    $auth = preg_grep('#^Authorization#', $headers);
                    $etag = preg_grep('#^If-Match:#', $headers);
                    if (!$content || !$version || !$auth || !$etag) {
                        return false;
                    }
                    if (array_diff($headers, $content + $auth + $version + $etag)) {
                        return false;
                    }
                    return true;
                }),
                $this->anything(),
                $this->equalTo(false)
        )
        ->will($this->returnValue(null));

        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz');
        $registration->setRegistrationId('2372532420827572008-85883004107185159-4');
        $registration->setEtag('3');
        $result = $this->mock->deleteRegistration($registration);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteRegistrationWithNoRegistrationId()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                      ->setEtag('3');

        $this->mock->deleteRegistration($registration);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteRegistrationWithEtag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setRegistrationId('2372532420827572008-85883004107185159-4');

        $this->mock->deleteRegistration($registration);
    }

    public function testCreateRegistrationId()
    {
        $this->mock->expects($this->once())
        ->method('request')
        ->with($this->equalTo('GET'),
                $this->equalTo('https://buildhub-ns.servicebus.windows.net/myHub/registrationIDs/?api-version=2013-08'),
                $this->callback(function ($headers) {
                    $content = preg_grep('#^Content-Type: application/atom\+xml;type=entry;charset=utf-8$#', $headers);
                    $version = preg_grep('#^x-ms-version: 2013-08$#', $headers);
                    $auth = preg_grep('#^Authorization#', $headers);
                    if (!$content || !$version || !$auth) {
                        return false;
                    }
                    if (array_diff($headers, $content + $auth + $version)) {
                        return false;
                    }
                    return true;
                }),
                $this->anything(),
                $this->equalTo(true)
        )
        ->will($this->returnValue('Content-Type: application/atom+xml;type=entry;charset=utf-8
Content-Location: https://buildhub-ns.servicebus.windows.net/myHub/registrations/2372532420827572008-85883004107185159-4
ETag: W/"3"'));

        $result = $this->mock->createRegistrationId();
        $this->assertEquals('2372532420827572008-85883004107185159-4', $result);
    }
}
