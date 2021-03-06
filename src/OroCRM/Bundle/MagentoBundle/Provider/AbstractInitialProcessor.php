<?php

namespace OroCRM\Bundle\MagentoBundle\Provider;

use Doctrine\ORM\EntityManager;

use Oro\Bundle\IntegrationBundle\Entity\Channel as Integration;
use Oro\Bundle\IntegrationBundle\Entity\Repository\ChannelRepository;
use Oro\Bundle\IntegrationBundle\Entity\Status;
use Oro\Bundle\IntegrationBundle\Provider\SyncProcessor;
use OroCRM\Bundle\MagentoBundle\Entity\MagentoSoapTransport;

abstract class AbstractInitialProcessor extends SyncProcessor
{
    const INITIAL_SYNC_START_DATE = 'initialSyncStartDate';
    const INITIAL_SYNCED_TO = 'initialSyncedTo';
    const CONNECTORS_INITIAL_SYNCED_TO = 'connectorsInitialSyncedTo';
    const START_SYNC_DATE = 'start_sync_date';
    const INTERVAL = 'initialSyncInterval';

    /** @var string */
    protected $channelClassName;

    /**
     * @param string $channelClassName
     */
    public function setChannelClassName($channelClassName)
    {
        $this->channelClassName = $channelClassName;
    }

    /**
     * @param Integration $integration
     * @return \DateTime
     */
    protected function getInitialSyncStartDate(Integration $integration)
    {
        if ($this->isInitialSyncStarted($integration)) {
            /** @var MagentoSoapTransport $transport */
            $transport = $integration->getTransport();

            return $transport->getInitialSyncStartDate();
        } else {
            return new \DateTime('now', new \DateTimeZone('UTC'));
        }
    }

    /**
     * @param Integration $integration
     * @return bool
     */
    protected function isInitialSyncStarted(Integration $integration)
    {
        /** @var MagentoSoapTransport $transport */
        $transport = $integration->getTransport();

        return (bool)$transport->getInitialSyncStartDate();
    }

    /**
     * @param object $entity
     */
    protected function saveEntity($entity)
    {
        /** @var EntityManager $em */
        $em = $this->doctrineRegistry->getManager();
        $em->persist($entity);
        $em->flush($entity);
    }

    /**
     * @param Integration $integration
     * @param string $connector
     * @param int|null $code
     * @return null|Status
     */
    protected function getLastStatusForConnector(Integration $integration, $connector, $code = null)
    {
        return $this->getChannelRepository()->getLastStatusForConnector($integration, $connector, $code);
    }

    /**
     * @param Integration $integration
     * @param string $connector
     * @return bool|\DateTime
     */
    protected function getSyncedTo(Integration $integration, $connector)
    {
        $lastStatus = $this->getLastStatusForConnector($integration, $connector, Status::STATUS_COMPLETED);
        if ($lastStatus) {
            $statusData = $lastStatus->getData();
            if (!empty($statusData[self::INITIAL_SYNCED_TO])) {
                return \DateTime::createFromFormat(
                    \DateTime::ISO8601,
                    $statusData[self::INITIAL_SYNCED_TO],
                    new \DateTimeZone('UTC')
                );
            }
        }

        return false;
    }

    /**
     * @return ChannelRepository
     */
    protected function getChannelRepository()
    {
        if (!$this->channelClassName) {
            throw new \InvalidArgumentException('Channel class option is missing');
        }

        return $this->doctrineRegistry->getRepository($this->channelClassName);
    }
}
