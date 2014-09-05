<?php

namespace OroCRM\Bundle\MarketingListBundle\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Oro\Bundle\EntityBundle\Provider\ExclusionProviderInterface;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;

/**
 * Provide exclude logic to filter entities with "contact_information" data
 */
class ContactInformationExclusionProvider implements ExclusionProviderInterface
{
    /**
     * @var ConfigProvider
     */
    protected $entityConfigProvider;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @param ConfigProvider  $entityConfigProvider
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ConfigProvider $entityConfigProvider, ManagerRegistry $managerRegistry)
    {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->managerRegistry      = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function isIgnoredEntity($className)
    {
        $entityConfig = $this->entityConfigProvider->getConfig($className);

        if ($entityConfig->has('contact_information')) {
            return false;
        }

        /** @var ClassMetadataInfo $metadata */
        $metadata = $this->managerRegistry->getManagerForClass($className)->getClassMetadata($className);
        foreach ($metadata->getFieldNames() as $fieldName) {
            $fieldConfig = $this->entityConfigProvider->getConfig($className, $fieldName);
            if ($fieldConfig->has('contact_information')) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isIgnoredField(ClassMetadata $metadata, $fieldName)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isIgnoredRelation(ClassMetadata $metadata, $associationName)
    {
        return false;
    }
}
