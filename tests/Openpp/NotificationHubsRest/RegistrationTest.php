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

        $this->assertXmlStringEqualsXmlString($expected, $payload);

        return $registration;
    }

    /**
     * @depends testGcmRegistration
     */
    public function testGcmRegistrationWithSingleTag(Registration $registration)
    {
        $registration->setTags('android');
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

    /**
     * @depends testGcmRegistration
     */
    public function testGcmRegistrationWithMultiTag(Registration $registration)
    {
        $registration->setTags(array('android', 'male', 'japanese'));
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
}