<?php

namespace Openpp\NotificationHubsRest\Registration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class AppleRegistration extends AbstractRegistration
{
    protected $expiry;

    /**
     * {@inheritdoc}
     */
    public function getRegistrationDescriptionTag()
    {
        return 'AppleRegistrationDescription';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateRegistrationDescriptionTag()
    {
        return 'AppleTemplateRegistrationDescription';
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenTag()
    {
        return 'DeviceToken';
    }

    /**
     * Sets the expiry for the template.
     *
     * @param string $expiry
     *
     * @return AppleRegistration this object
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function appendAdditionalNode($descriptionNode)
    {
        if ($this->template && $this->expiry) {
            $descriptionNode->appendChild($this->dom->createElement('Expiry', $this->expiry));
        }
    }
}