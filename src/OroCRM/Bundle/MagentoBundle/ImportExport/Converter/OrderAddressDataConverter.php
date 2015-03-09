<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Converter;

class OrderAddressDataConverter extends AbstractAddressDataConverter
{
    /**
     * {@inheritdoc}
     */
    protected function getHeaderConversionRules()
    {
        return array_merge(
            parent::getHeaderConversionRules(),
            [
                'fax'          => 'fax',
                'telephone'    => 'phone',
                'company'      => 'organization',
                'customer_id'  => 'customerId',
                'address_type' => 'types:0:name'
            ]
        );
    }
}
