<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Serializer;

use Oro\Bundle\ImportExportBundle\Serializer\Normalizer\DenormalizerInterface;
use OroCRM\Bundle\MagentoBundle\Entity\OrderItem;
use OroCRM\Bundle\MagentoBundle\Provider\MagentoConnectorInterface;

class OrderItemDenormalizer extends AbstractNormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        /** @var OrderItem $object */
        $className = MagentoConnectorInterface::ORDER_ITEM_TYPE;
        $object    = new $className();
        $this->fillResultObject($object, $data);
        if ($object->getDiscountPercent()) {
            $object->setDiscountPercent($object->getDiscountPercent() / 100);
        }
        if ($object->getTaxPercent()) {
            $object->setTaxPercent($object->getTaxPercent() / 100);
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = array())
    {
        return $type == MagentoConnectorInterface::ORDER_ITEM_TYPE;
    }
}
