<?php

namespace Openpp\NotificationHubsRest\Registration\Tests;

use Openpp\NotificationHubsRest\Registration\GcmRegistration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class GcmRegistrationTest extends \PHPUnit_Framework_TestCase
{
    public function testRegistration()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId> 
        </GcmRegistrationDescription>
    </content>
</entry>
XML;
        //fwrite(STDERR, print_r($payload, TRUE));

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testRegistrationWithSingleTag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags('android');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>android</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId>
        </GcmRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testRegistrationWithMultiTag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('android', 'male', 'japanese'));
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>android,male,japanese</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId>
        </GcmRegistrationDescription>
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
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTemplate($this->getTemplate());
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId> 
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
        </GcmTemplateRegistrationDescription>
    </content>
</entry>
XML;
        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithSingleTag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags('android')
                     ->setTemplate($this->getTemplate());
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>android</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId> 
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
        </GcmTemplateRegistrationDescription>
    </content>
</entry>
XML;

        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithMultiTag()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('android', 'male', 'japanese'))
                     ->setTemplate($this->getTemplate());
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>android,male,japanese</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId> 
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
        </GcmTemplateRegistrationDescription>
    </content>
</entry>
XML;

        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoToken()
    {
        $registration = new GcmRegistration();
        $payload = $registration->getPayload();
    }

    public function testBuildUriWithNoRegistrationId()
    {
        $registration = new GcmRegistration();

        $uri = $registration->buildUri('aaa.servicebus.windows.net/', 'myhub');
        $this->assertEquals('aaa.servicebus.windows.net/myhub/registrations/', $uri);
    }
    
    public function testBuildUriWithRegistrationId()
    {
        $registration = new GcmRegistration();
        $registration->setRegistrationId('abcdefg');

        $uri = $registration->buildUri('aaa.servicebus.windows.net/', 'myhub');
        $this->assertEquals('aaa.servicebus.windows.net/myhub/registrations/abcdefg', $uri);
    }
    
    public function testGetContentType()
    {
        $registration = new GcmRegistration();
        $this->assertEquals('application/atom+xml;type=entry;charset=utf-8', $registration->getContentType());
    }
    
    public function testGetHeadersWithNoEtag()
    {
        $registration = new GcmRegistration();

        $this->assertEquals(array(
            'Content-Type: application/atom+xml;type=entry;charset=utf-8',
            'x-ms-version: ' . '2013-08',
        ),
                $registration->getHeaders());
    }
    
    public function testGetHeadersWithEtag()
    {
        $registration = new GcmRegistration();
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
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('android', 'male', 'japanese'));

        $response = <<<RESPONSE
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
</entry>
RESPONSE;

        $result = $registration->scrapeResponse($response);

        $this->assertEquals(array(
            'ETag' => '3',
            'ExpirationTime' => '2014-09-01T15:57:46.778Z',
            'RegistrationId' => '2372532420827572008-85883004107185159-4',
            'Tags' => 'android, male, japanese',
            'GcmRegistrationId' => 'abcdefghijklmnopqrstuvwxyz',
        ), $result);
    }
    
    public function testScrapeTemplateRegistrationResponse()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('android', 'male', 'japanese'))
                     ->setTemplate('{ "gcm": { "data": "$(message)"} }');

        $response = <<<RESPONSE
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>android, male, japanese</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId>
            <BodyTemplate><![CDATA[{ "gcm": { "data": "$(message)"} }]]></BodyTemplate>
        </GcmTemplateRegistrationDescription>
    </content>
</entry>
RESPONSE;

        $result = $registration->scrapeResponse($response);

        $this->assertEquals(array(
            'ETag' => '3',
            'ExpirationTime' => '2014-09-01T15:57:46.778Z',
            'RegistrationId' => '2372532420827572008-85883004107185159-4',
            'Tags' => 'android, male, japanese',
            'GcmRegistrationId' => 'abcdefghijklmnopqrstuvwxyz',
            'BodyTemplate' => '{ "gcm": { "data": "$(message)"} }'
        ), $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testScrapeRegistrationResponseWithInvalidDescription()
    {
        $registration = new GcmRegistration();
        $registration->setToken('abcdefghijklmnopqrstuvwxyz')
                     ->setTags(array('android', 'male', 'japanese'));

        $response = <<<RESPONSE
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <GcmTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>android, male, japanese</Tags>
            <GcmRegistrationId>abcdefghijklmnopqrstuvwxyz</GcmRegistrationId>
        </GcmTemplateRegistrationDescription>
    </content>
</entry>
RESPONSE;

        $registration->scrapeResponse($response);
    }
}
