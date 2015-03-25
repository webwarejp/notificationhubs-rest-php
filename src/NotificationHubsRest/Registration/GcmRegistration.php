<?php

namespace Openpp\NotificationHubsRest\Registration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class GcmRegistration extends AbstractRegistration
{
    /**
     * {@inheritdoc}
     */
    public function getRegistrationDescriptionTag()
    {
        return 'GcmRegistrationDescription';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateRegistrationDescriptionTag()
    {
        return 'GcmTemplateRegistrationDescription';
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenTag()
    {
        return 'GcmRegistrationId';
    }
}