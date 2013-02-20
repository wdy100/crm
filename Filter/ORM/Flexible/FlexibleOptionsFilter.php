<?php

namespace Oro\Bundle\GridBundle\Filter\ORM\Flexible;

use Doctrine\Common\Persistence\ObjectRepository;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Oro\Bundle\FlexibleEntityBundle\Entity\Repository\FlexibleEntityRepository;
use Oro\Bundle\GridBundle\Datagrid\ORM\ProxyQuery;
use Oro\Bundle\FlexibleEntityBundle\Entity\Attribute;
use Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption;

class FlexibleOptionsFilter extends AbstractFlexibleFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $proxyQuery, $alias, $field, $value)
    {
        if (!$value || !is_array($value) || !array_key_exists('value', $value) || null === $value['value']) {
            return;
        }

        $value['value'] = trim($value['value']);

        if (strlen($value['value']) == 0) {
            return;
        }

        /** @var $proxyQuery ProxyQuery */
        $queryBuilder = $proxyQuery->getQueryBuilder();

        /** @var $entityRepository FlexibleEntityRepository */
        $entityRepository = $this->flexibleManager->getFlexibleRepository();
        $entityRepository->applyFilterByOptionAttribute($queryBuilder, $field, $value['value']);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return array('oro_grid_type_filter_flexible_options', array(
            'label'         => $this->getLabel(),
            'field_options' => array('choices' => $this->getFieldChoices()),
        ));
    }

    /**
     * @return array
     * @throws \LogicException
     */
    protected function getFieldChoices()
    {
        $filedName = $this->getOption('field_name');

        /** @var $attributeRepository ObjectRepository */
        $attributeRepository = $this->flexibleManager->getAttributeRepository();
        /** @var $attribute Attribute */
        $attribute = $attributeRepository->findOneBy(array('code' => $filedName));
        if (!$attribute) {
            throw new \LogicException('There is no flexible attribute with name ' . $filedName . '.');
        }

        /** @var $optionsRepository ObjectRepository */
        $optionsRepository = $this->flexibleManager->getAttributeOptionRepository();
        $options = $optionsRepository->findBy(
            array('attribute' => $attribute)
        );

        $choices = array();
        /** @var $option AttributeOption */
        foreach ($options as $option) {
            $optionValue = $option->getOptionValue();
            if ($optionValue) {
                $choices[$option->getId()] = $optionValue->getValue();
            }
        }

        return $choices;
    }
}
