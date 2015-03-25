<?php

namespace Openpp\NotificationHubsRest\Registration\Tests;

use Openpp\NotificationHubsRest\Registration\AppleRegistration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class AppleRegistrationTest extends \PHPUnit_Framework_TestCase
{
    public function testRegistration()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken> 
        </AppleRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testRegistrationWithSingleTag()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags('ios');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios</Tags>
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken> 
        </AppleRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testAppleRegistrationWithMultiTag()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('ios', 'female', 'japanese'));
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios,female,japanese</Tags>
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken> 
        </AppleRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    protected function getTemplate()
    {
        $template = <<<TEMPLATE
<toast>
  <visual>
    <binding template=\"ToastText01\">
      <text id=\"1\">$(News_English)</text>
    </binding>
  </visual>
</toast>
TEMPLATE;

        return $template;
    }

    public function testTemplateRegistration()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTemplate($this->getTemplate());
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
        </AppleTemplateRegistrationDescription>
    </content>
</entry>
XML;
        $expected = sprintf($expected, $this->getTemplate());

//        fwrite(STDERR, print_r($payload, TRUE));

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithSingleTag()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags('ios')
                     ->setTemplate($this->getTemplate());
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios</Tags>
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
        </AppleTemplateRegistrationDescription>
    </content>
</entry>
XML;

        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithMultiTag()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('ios', 'female', 'japanese'))
                     ->setTemplate($this->getTemplate());
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios,female,japanese</Tags>
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
        </AppleTemplateRegistrationDescription>
    </content>
</entry>
XML;

        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithExpiry()
    {
        $expiry = time() + (60*60);
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTemplate($this->getTemplate())
                     ->setExpiry($expiry);
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
            <Expiry>%d</Expiry>
        </AppleTemplateRegistrationDescription>
    </content>
</entry>
XML;
        $expected = sprintf($expected, $this->getTemplate(), $expiry);

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoToken()
    {
        $registration = new AppleRegistration();
        $payload = $registration->getPayload();
    }

    public function testBuildUriWithNoRegistrationId()
    {
        $registration = new AppleRegistration();

        $uri = $registration->buildUri('aaa.servicebus.windows.net/', 'myhub');
        $this->assertEquals('aaa.servicebus.windows.net/myhub/registrations/', $uri);
    }

    public function testBuildUriWithRegistrationId()
    {
        $registration = new AppleRegistration();
        $registration->setRegistrationId('abcdefg');
    
        $uri = $registration->buildUri('aaa.servicebus.windows.net/', 'myhub');
        $this->assertEquals('aaa.servicebus.windows.net/myhub/registrations/abcdefg', $uri);
    }

    public function testGetContentType()
    {
        $registration = new AppleRegistration();
        $this->assertEquals('application/atom+xml;type=entry;charset=utf-8', $registration->getContentType());
    }

    public function testGetHeadersWithNoEtag()
    {
        $registration = new AppleRegistration();

        $this->assertEquals(array(
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: ' . '2013-08',
        ), 
        $registration->getHeaders());
    }

    public function testGetHeadersWithEtag()
    {
        $registration = new AppleRegistration();
        $registration->setETag('abcdefg');

        $this->assertEquals(array(
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: 2013-08',
            'If-Match: abcdefg',
        ),
        $registration->getHeaders());
    }

    public function testScrapeRegistrationResponse()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('ios', 'female', 'japanese'));

        $response = <<<RESPONSE
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>ios, female, japanese</Tags>
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken>
        </AppleRegistrationDescription>
    </content>
</entry>
RESPONSE;

        $result = $registration->scrapeResponse($response);
        //fwrite(STDERR, print_r($result, TRUE));
        $this->assertEquals(array(
            'ETag' => '3',
            'ExpirationTime' => '2014-09-01T15:57:46.778Z',
            'RegistrationId' => '2372532420827572008-85883004107185159-4',
            'Tags' => 'ios, female, japanese',
            'DeviceToken' => 'abcdefghijklmnopqrstuvwxyz',
        ), $result);
    }

    public function testScrapeTemplateRegistrationResponse()
    {
        $registration = new AppleRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                      ->setTags(array('ios', 'female', 'japanese'))
                      ->setTemplate('{ "aps": { "alert": "$(message)"} }')
                      ->setExpiry(3000);

        $response = <<<RESPONSE
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <AppleTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>ios, female, japanese</Tags>
            <DeviceToken>abcdefghijklmnopqrstuvwxyz</DeviceToken>
            <BodyTemplate><![CDATA[{ "aps": { "alert": "$(message)"} }]]></BodyTemplate>
            <Expiry>3000</Expiry>
        </AppleTemplateRegistrationDescription>
    </content>
</entry>
RESPONSE;
    
        $result = $registration->scrapeResponse($response);
        //fwrite(STDERR, print_r($result, TRUE));
        $this->assertEquals(array(
            'ETag' => '3',
            'ExpirationTime' => '2014-09-01T15:57:46.778Z',
            'RegistrationId' => '2372532420827572008-85883004107185159-4',
            'Tags' => 'ios, female, japanese',
            'DeviceToken' => 'abcdefghijklmnopqrstuvwxyz',
            'BodyTemplate' => '{ "aps": { "alert": "$(message)"} }',
            'Expiry' => '3000'
        ), $result);
    }
}
