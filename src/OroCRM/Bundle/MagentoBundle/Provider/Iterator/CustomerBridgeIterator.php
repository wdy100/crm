<?php

namespace OroCRM\Bundle\MagentoBundle\Provider\Iterator;

use OroCRM\Bundle\MagentoBundle\Provider\BatchFilterBag;
use OroCRM\Bundle\MagentoBundle\Provider\Dependency\CustomerDependencyManager;
use OroCRM\Bundle\MagentoBundle\Provider\Transport\SoapTransport;

class CustomerBridgeIterator extends AbstractBridgeIterator
{
    /**
     * {@inheritdoc}
     */
    protected function applyFilter()
    {
        if ($this->websiteId !== StoresSoapIterator::ALL_WEBSITES) {
            $this->filter->addWebsiteFilter([$this->websiteId]);
        }
        parent::applyFilter();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityIds()
    {
        $this->applyFilter();

        $filters = $this->filter->getAppliedFilters();
        $filters['pager'] = ['page' => $this->getCurrentPage(), 'pageSize' => $this->pageSize];

        $this->loadByFilters($filters);

        return array_keys($this->entityBuffer);
    }

    /**
     * @param array $ids
     */
    protected function loadEntities(array $ids)
    {
        if (!$ids) {
            return;
        }

        $filters = new BatchFilterBag();
        $filters->addComplexFilter(
            'in',
            [
                'key' => $this->getIdFieldName(),
                'value' => [
                    'key' => 'in',
                    'value' => implode(',', $ids)
                ]
            ]
        );

        if (null !== $this->websiteId && $this->websiteId !== StoresSoapIterator::ALL_WEBSITES) {
            $filters->addWebsiteFilter([$this->websiteId]);
        }

        $this->loadByFilters($filters->getAppliedFilters());
    }

    /**
     * @param array $filters
     */
    protected function loadByFilters(array $filters)
    {
        $result = $this->transport->call(SoapTransport::ACTION_ORO_CUSTOMER_LIST, $filters);
        $result = $this->processCollectionResponse($result);

        $resultIds = array_map(
            function (&$item) {
                $item->addresses = $this->processCollectionResponse($item->addresses);

                return $item->customer_id;
            },
            $result
        );
        $this->entityBuffer = array_combine($resultIds, $result);
    }

    /**
     * {@inheritdoc}
     */
    protected function addDependencyData($result)
    {
        CustomerDependencyManager::addDependencyData($result, $this->transport);
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdFieldName()
    {
        return 'customer_id';
    }
}
