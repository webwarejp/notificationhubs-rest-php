<?php

namespace Openpp\NotificationHubsRest\Tests;

use Openpp\NotificationHubsRest\Registration;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    public function testGcmRegistration()
    {
        $registration = new Registration(Registration::TYPE_GCM);
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
        fwrite(STDERR, print_r($payload, TRUE));

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testGcmRegistrationWithSingleTag()
    {
        $registration = new Registration(Registration::TYPE_GCM);
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

    public function testGcmRegistrationWithMultiTag()
    {
        $registration = new Registration(Registration::TYPE_GCM);
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

    public function testGcmTemplateRegistration()
    {
        $registration = new Registration(Registration::TYPE_GCM);
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

    public function testGcmTemplateRegistrationWithSingleTag()
    {
        $registration = new Registration(Registration::TYPE_GCM);
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

    public function testGcmTemplateRegistrationWithMultiTag()
    {
        $registration = new Registration(Registration::TYPE_GCM);
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

    public function testAppleRegistration()
    {
        $registration = new Registration(Registration::TYPE_APPLE);
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

    public function testAppleRegistrationWithSingleTag()
    {
        $registration = new Registration(Registration::TYPE_APPLE);
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
        $registration = new Registration(Registration::TYPE_APPLE);
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

    public function testAppleTemplateRegistration()
    {
        $registration = new Registration(Registration::TYPE_APPLE);
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

        $this->assertXmlStringEqualsXmlString($expected, $payload);
    }

    public function testAppleTemplateRegistrationWithSingleTag()
    {
        $registration = new Registration(Registration::TYPE_APPLE);
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

    public function testAppleTemplateRegistrationWithMultiTag()
    {
        $registration = new Registration(Registration::TYPE_APPLE);
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

    public function testAppleTemplateRegistrationWithExpiry()
    {
        $expiry = time() + (60*60);
        $registration = new Registration(Registration::TYPE_APPLE);
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
        $registration = new Registration(Registration::TYPE_APPLE);
        $payload = $registration->getPayload();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidType()
    {
        $registration = new Registration('blackberry');
        $registration->setToken('abcdefghijklmnopqrstuvwxyz');
        $payload = $registration->getPayload();
    }
}