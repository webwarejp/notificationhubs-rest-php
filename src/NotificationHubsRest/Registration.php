<?php

namespace Openpp\NotificationHubsRest;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class Registration
{
    const TYPE_GCM =   "gcm";
    const TYPE_APPLE = "apple";

    protected $type;
    protected $dom;
    protected $content;
    protected $token;
    protected $tags;
    protected $template;
    protected $expiry;

    /**
     * Constructor
     */
    public function __construct($type)
    {
        $this->type = $type;

        $this->dom = new \DomDocument('1.0', 'utf-8');

        $entryElement = $this->dom->createElement('entry');
        $entryElement->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');

        $contentElement = $this->dom->createElement('content');
        $contentElement->setAttribute('type', 'application/xml');

        $this->content = $this->dom
            ->appendChild($entryElement)
            ->appendChild($contentElement)
        ;
    }

    /**
     * Sets Gcm Registration ID or APNs Device token.
     *
     * @param string $token
     *
     * @return Registration this object
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Sets the tags.
     *
     * @param mixed $tags
     *
     * @return Registration this object
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Returns the tags.
     *
     * @return string
     */
    private function getTags()
    {
        if (is_array($this->tags)) {
            return implode(',', $this->tags);
        }

        return $this->tags;
    }

    /**
     * Sets the template for the body.
     *
     * @param string $template
     *
     * @return Registration this object
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Sets the expiry for the template (APNs only).
     *
     * @param string $expiry
     *
     * @return Registration this object
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Returns the atom payload for the registration request.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getPayload()
    {
        if (!$this->token) {
            throw new \RuntimeException('Gcm Registration ID or APNs Device token is mandatory.');
        }

        switch ($this->type) {
            case self::TYPE_GCM:
                if ($this->template) {
                    $descriptionTag = 'GcmTemplateRegistrationDescription';
                } else {
                    $descriptionTag = 'GcmRegistrationDescription';
                }

                $deviceIdTag = 'GcmRegistrationId';

                break;

            case self::TYPE_APPLE:
                if ($this->template) {
                    $descriptionTag = 'AppleTemplateRegistrationDescription';
                } else {
                    $descriptionTag = 'AppleRegistrationDescription';
                }

                $deviceIdTag = 'DeviceToken';

                break;

            default:
                throw new \RuntimeException('Invalid type: ' . $this->type);
        }

        $descriptionElement = $this->dom->createElement($descriptionTag);
        $descriptionElement->setAttribute('xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance');
        $descriptionElement->setAttribute('xmlns', 'http://schemas.microsoft.com/netservices/2010/10/servicebus/connect');

        $desc = $this->content->appendChild($descriptionElement);

        if ($this->tags) {
            $desc->appendChild(
                $this->dom->createElement('Tags', $this->getTags())
            );
        }

        $desc->appendChild($this->dom->createElement($deviceIdTag, $this->token));

        if ($this->template) {
            $cdata = $this->dom->createCDATASection($this->template);
            $bodyTemplateElement = $this->dom->createElement('BodyTemplate');
            $bodyTemplateElement->appendChild($cdata);
            $desc->appendChild($bodyTemplateElement);

            if (self::TYPE_APPLE == $this->type && $this->expiry) {
                $desc->appendChild($this->dom->createElement('Expiry', $this->expiry));
            }
        }

        $this->dom->formatOutput = true;

        return $this->dom->saveXML();
    }
}