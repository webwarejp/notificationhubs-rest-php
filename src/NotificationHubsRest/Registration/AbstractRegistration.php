<?php

namespace Openpp\NotificationHubsRest\Registration;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
abstract class AbstractRegistration implements RegistrationInterface
{
    protected $dom;
    protected $content;
    protected $token;
    protected $tags;
    protected $template;
    protected $registrationId;
    protected $eTag;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return RegistrationInterface this object
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
     * @return RegistrationInterface this object
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
     * @return RegistrationInterface this object
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Sets the registration ID for update or delete registration API.
     *
     * @param string $registrationId
     *
     * @return RegistrationInterface this object
     */
    public function setRegistrationId($registrationId)
    {
        $this->registrationId = $registrationId;

        return $this;
    }

    /**
     * Returns the token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns the registration ID.
     *
     * @return string
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * Sets the ETag for update or delete registration API.
     *
     * @param string $eTag
     *
     * @return RegistrationInterface this object
     */
    public function setETag($eTag)
    {
        $this->eTag = $eTag;

        return $this;
    }

    /**
     * Returns the ETag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->eTag;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUri($endpoint, $hubPath)
    {
        $uri = $endpoint . $hubPath . '/registrations/';

        if ($this->registrationId) {
            $uri .= $this->registrationId;
        }

        return $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'application/atom+xml;type=entry;charset=utf-8';
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        $headers = array(
            'Content-Type: ' . $this->getContentType(),
            'x-ms-version: ' . '2015-01'
        );

        if ($this->eTag) {
            $headers[] = 'If-Match: ' . $this->eTag;
        }

        return $headers;
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
            throw new \RuntimeException('Token is mandatory.');
        }

        $descriptionNode = $this->appendDescriptionNode();
        $this->appendTagNode($descriptionNode);
        $this->appendTokenNode($descriptionNode);
        $this->appendTemplateNode($descriptionNode);
        $this->appendAdditionalNode($descriptionNode);

        $this->dom->formatOutput = true;

        return $this->dom->saveXML();
    }

    /**
     * Appends the registration description DOMNode.
     *
     * @return DOMNode
     */
    protected function appendDescriptionNode()
    {
        if ($this->template) {
            $descriptionTag = $this->getTemplateRegistrationDescriptionTag();
        } else {
            $descriptionTag = $this->getRegistrationDescriptionTag();
        }

        $descriptionElement = $this->dom->createElement($descriptionTag);
        $descriptionElement->setAttribute('xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance');
        $descriptionElement->setAttribute('xmlns', 'http://schemas.microsoft.com/netservices/2010/10/servicebus/connect');

        return $this->content->appendChild($descriptionElement);
    }

    /**
     * Appends the 'Tags' DOMNode.
     *
     * @param DOMNode $descriptionNode
     */
    protected function appendTagNode($descriptionNode)
    {
        if ($this->tags) {
            $descriptionNode->appendChild(
                $this->dom->createElement('Tags', $this->getTags())
            );
        }
    }

    /**
     * Appends the token DOMNode.
     *
     * @param DOMNode $descriptionNode
     */
    protected function appendTokenNode($descriptionNode)
    {
        $descriptionNode->appendChild($this->dom->createElement($this->getTokenTag(), $this->token));
    }

    /**
     * Appends the 'BodyTemplate' DOMNode.
     *
     * @param DOMNode $descriptionNode
     */
    protected function appendTemplateNode($descriptionNode)
    {
        if ($this->template) {
            $cdata = $this->dom->createCDATASection($this->template);
            $bodyTemplateElement = $this->dom->createElement('BodyTemplate');
            $bodyTemplateElement->appendChild($cdata);
            $descriptionNode->appendChild($bodyTemplateElement);
        }
    }

    /**
     * Appends the additional DOMNode.
     *
     * @param DOMNode $descriptionNode
     */
    protected function appendAdditionalNode($descriptionNode)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function scrapeResponse($response)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($response);

        if ($this->template) {
            $descriptionTag = $this->getTemplateRegistrationDescriptionTag();
        } else {
            $descriptionTag = $this->getRegistrationDescriptionTag();
        }

        $description = $dom->getElementsByTagName($descriptionTag)->item(0);
        if (!$description) {
            throw new \RuntimeException("Could not find '" . $descriptionTag . "' tag in the response: " . $response);
        }

        $result = array();
        foreach ($description->childNodes as $child) {
            if ($child->nodeName != '#text') {
                $result[$child->nodeName] = $child->nodeValue;
            }
        }

        return $result;
    }
}
