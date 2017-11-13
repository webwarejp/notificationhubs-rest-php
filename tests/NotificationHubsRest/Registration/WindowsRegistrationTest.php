<?php

namespace Openpp\NotificationHubsRest\Registration\Tests;

use Openpp\NotificationHubsRest\Registration\WindowsRegistration;

class WindowsRegistrationTest extends \PHPUnit_Framework_TestCase
{
    public function testRegistration()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
        </WindowsRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testRegistrationWithSingleTag()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTags('ios');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios</Tags>
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
        </WindowsRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testWindowsRegistrationWithMultiTag()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTags(['ios', 'female', 'japanese']);
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios,female,japanese</Tags>
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
        </WindowsRegistrationDescription>
    </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistration()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTemplate($this->getTemplate())
                     ->setWnsType('wns/tile')
                     ->setWnsTag('myTag');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
            <WnsHeaders>
                <WnsHeader>
                    <Header>X-WNS-Type</Header>
                    <Value>wns/tile</Value>
                </WnsHeader>
                <WnsHeader>
                    <Header>X-WNS-Tag</Header>
                    <Value>myTag</Value>
                </WnsHeader>
            </WnsHeaders>
        </WindowsTemplateRegistrationDescription>
    </content>
</entry>
XML;
        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithSingleTag()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTags('ios')
                     ->setTemplate($this->getTemplate())
                     ->setWnsType('wns/tile')
                     ->setWnsTag('myTag');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios</Tags>
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
            <WnsHeaders>
                <WnsHeader>
                    <Header>X-WNS-Type</Header>
                    <Value>wns/tile</Value>
                </WnsHeader>
                <WnsHeader>
                    <Header>X-WNS-Tag</Header>
                    <Value>myTag</Value>
                </WnsHeader>
            </WnsHeaders>
        </WindowsTemplateRegistrationDescription>
    </content>
</entry>
XML;

        $expected = sprintf($expected, $this->getTemplate());

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testTemplateRegistrationWithMultiTag()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTags(['ios', 'female', 'japanese'])
                     ->setTemplate($this->getTemplate())
                     ->setWnsType('wns/tile')
                     ->setWnsTag('myTag');
        $payload = $registration->getPayload();

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <Tags>ios,female,japanese</Tags>
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
            <BodyTemplate><![CDATA[%s]]></BodyTemplate>
            <WnsHeaders>
                <WnsHeader>
                    <Header>X-WNS-Type</Header>
                    <Value>wns/tile</Value>
                </WnsHeader>
                <WnsHeader>
                    <Header>X-WNS-Tag</Header>
                    <Value>myTag</Value>
                </WnsHeader>
            </WnsHeaders>
        </WindowsTemplateRegistrationDescription>
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
        $registration = new WindowsRegistration();
        $registration->getPayload();
    }

    public function testBuildUriWithNoRegistrationId()
    {
        $registration = new WindowsRegistration();

        $uri = $registration->buildUri('aaa.servicebus.windows.net/', 'myhub');
        $this->assertEquals('aaa.servicebus.windows.net/myhub/registrations/', $uri);
    }

    public function testBuildUriWithRegistrationId()
    {
        $registration = new WindowsRegistration();
        $registration->setRegistrationId('abcdefg');

        $uri = $registration->buildUri('aaa.servicebus.windows.net/', 'myhub');
        $this->assertEquals('aaa.servicebus.windows.net/myhub/registrations/abcdefg', $uri);
    }

    public function testGetContentType()
    {
        $registration = new WindowsRegistration();
        $this->assertEquals('application/atom+xml;type=entry;charset=utf-8', $registration->getContentType());
    }

    public function testGetHeadersWithNoEtag()
    {
        $registration = new WindowsRegistration();

        $this->assertEquals(
            [
                'Content-Type: application/atom+xml;type=entry;charset=utf-8',
                'x-ms-version: '.'2013-08',
            ],
            $registration->getHeaders()
        );
    }

    public function testGetHeadersWithEtag()
    {
        $registration = new WindowsRegistration();
        $registration->setETag('abcdefg');

        $this->assertEquals(
            [
                'Content-Type: application/atom+xml;type=entry;charset=utf-8',
                'x-ms-version: 2013-08',
                'If-Match: abcdefg',
            ],
            $registration->getHeaders()
        );
    }

    public function testScrapeRegistrationResponse()
    {
        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTags(['ios', 'female', 'japanese']);

        $response = <<<RESPONSE
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>ios, female, japanese</Tags>
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
        </WindowsRegistrationDescription>
    </content>
</entry>
RESPONSE;

        $result = $registration->scrapeResponse($response);
        $this->assertEquals(
            [
                'ETag' => '3',
                'ExpirationTime' => '2014-09-01T15:57:46.778Z',
                'RegistrationId' => '2372532420827572008-85883004107185159-4',
                'Tags' => 'ios, female, japanese',
                'ChannelUri' => 'http://channel.uri/endpoint',
            ],
            $result
        );
    }

    public function testScrapeTemplateRegistrationResponse()
    {
        self::markTestSkipped('Not yet ready');

        $registration = new WindowsRegistration();
        $registration->setToken('http://channel.uri/endpoint')
                     ->setTags(['ios', 'female', 'japanese'])
                     ->setTemplate('{ "aps": { "alert": "$(message)"} }')
                     ->setWnsType('wns/tile')
                     ->setWnsTag('myTag');

        $response = <<<RESPONSE
<entry xmlns="http://www.w3.org/2005/Atom">
    <content type="application/xml">
        <WindowsTemplateRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
            <ETag>3</ETag>
            <ExpirationTime>2014-09-01T15:57:46.778Z</ExpirationTime>
            <RegistrationId>2372532420827572008-85883004107185159-4</RegistrationId>
            <Tags>ios, female, japanese</Tags>
            <ChannelUri>http://channel.uri/endpoint</ChannelUri>
            <BodyTemplate><![CDATA[{ "aps": { "alert": "$(message)"} }]]></BodyTemplate>
            <WnsHeaders>
                <WnsHeader>
                    <Header>X-WNS-Type</Header>
                    <Value>wns/tile</Value>
                </WnsHeader>
                <WnsHeader>
                    <Header>X-WNS-Tag</Header>
                    <Value>myTag</Value>
                </WnsHeader>
            </WnsHeaders>
            <Expiry>3000</Expiry>
        </WindowsTemplateRegistrationDescription>
    </content>
</entry>
RESPONSE;

        $result = $registration->scrapeResponse($response);
        $this->assertEquals(
            [
                'ETag' => '3',
                'ExpirationTime' => '2014-09-01T15:57:46.778Z',
                'RegistrationId' => '2372532420827572008-85883004107185159-4',
                'Tags' => 'ios, female, japanese',
                'ChannelUri' => 'http://channel.uri/endpoint',
                'BodyTemplate' => '{ "aps": { "alert": "$(message)"} }',
                'Expiry' => '3000',
            ],
            $result
        );
    }

    protected function getTemplate()
    {
        $template = '{"aps":{"alert":"$(message)"}}';

        return $template;
    }
}
