<?php

namespace OroCRM\Bundle\MagentoBundle\Provider;

class NewsletterSubscriberConnector extends AbstractMagentoConnector implements ExtensionAwareInterface
{
    const IMPORT_JOB_NAME = 'mage_newsletter_subscriber_import';
    const TYPE = 'newsletter_subscriber';

    /** @var string */
    protected $className;

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'orocrm.magento.connector.newsletter_subscriber.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getImportEntityFQCN()
    {
        return $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportJobName()
    {
        return self::IMPORT_JOB_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnectorSource()
    {
        return $this->transport->getNewsletterSubscribers();
    }
}
