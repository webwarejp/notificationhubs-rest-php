<?php

namespace Openpp\NotificationHubsRest\Registration;

class WindowsRegistration extends AbstractRegistration
{
    /**
     * @var string
     */
    protected $wnsType;

    /**
     * @var string
     */
    protected $wnsTag;

    /**
     * {@inheritdoc}
     */
    public function getRegistrationDescriptionTag()
    {
        return 'WindowsRegistrationDescription';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateRegistrationDescriptionTag()
    {
        return 'WindowsTemplateRegistrationDescription';
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenTag()
    {
        return 'ChannelUri';
    }

    /**
     * Sets the X-WNS-Type header value.
     *
     * @param string $wnsType
     *
     * @return WindowsRegistration this object
     */
    public function setWnsType($wnsType)
    {
        $this->wnsType = $wnsType;

        return $this;
    }

    /**
     * Sets the X-WNS-Tag header value.
     *
     * @param string $wnsTag
     *
     * @return WindowsRegistration this object
     */
    public function setWnsTag($wnsTag)
    {
        $this->wnsTag = $wnsTag;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function appendAdditionalNode($descriptionNode)
    {
        if ($this->template) {
            $wnsHeadersElement = $this->dom->createElement('WnsHeaders');
            $wnsHeader = $this->dom->createElement('WnsHeader');
            $wnsHeader->appendChild($this->dom->createElement('Header', 'X-WNS-Type'));
            $wnsHeader->appendChild($this->dom->createElement('Value', $this->wnsType));
            $wnsHeadersElement->appendChild($wnsHeader);

            if ($this->wnsTag) {
                $wnsHeader = $this->dom->createElement('WnsHeader');
                $wnsHeader->appendChild($this->dom->createElement('Header', 'X-WNS-Tag'));
                $wnsHeader->appendChild($this->dom->createElement('Value', $this->wnsTag));
                $wnsHeadersElement->appendChild($wnsHeader);
            }

            $descriptionNode->appendChild($wnsHeadersElement);
        }
    }
}
