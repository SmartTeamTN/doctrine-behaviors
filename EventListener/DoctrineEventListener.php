<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 * @todo find a better solution to access entityManager
 */

namespace SmartTeam\DoctrineBehaviors\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use SmartTeam\DoctrineBehaviors\Model\Freezable;
use SmartTeam\DoctrineBehaviors\Model\HasFreezable;
use Exception;

/**
 * Class DoctrineEventListener
 *
 * @package SmartTeam\DoctrineBehaviors\EventListener
 */
class DoctrineEventListener
{
    /**
     * Do not delete this, it's used in doctrine types
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * DoctrineEventListener constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws Exception
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $hasFreezable = HasFreezable::hasFreezable($entity);
        if ($hasFreezable['status'] === true) {
            foreach ($entity->getFreezables() as $freezableName) {
                $freezableUpper = strtoupper(substr($freezableName, 0, 1)).substr($freezableName, 1, strlen($freezableName));
                $freezableObject = $entity->{'get'.$freezableUpper}();
                if ($freezableObject === null || !is_object($freezableObject)) continue;
                $freezable = Freezable::isFreezable($freezableObject);
                if (!$freezable['status']) {
                    throw new Exception($freezable['message']);
                }
                try {
                    $entityRepository = $this->getEntityManager()->getRepository($freezableObject->getMetadata()['class']);
                } catch (Exception $exception) {
                    $entity->{'set'.$freezableUpper}(null);
                    return;
                }

                try {
                    $currentEntity = $entityRepository->find($freezableObject->getMetadata()['data'][$freezableObject->getMetadata()['params']['id'] ?? 'id']);
                } catch (Exception $exception) {
                    $currentEntity = null;
                }
                unset($entityRepository);

                $metadata = $freezableObject->getMetadata();

                $unfrozenEntity = $freezableObject->unfreezeState($metadata, $currentEntity ?? null);
                if (!empty($metadata['references'])) {
                    foreach ($metadata['references'] as &$reference) {
                        if (isset($reference['retrieval'])) continue;
                        if (!isset($reference['type']) || $reference['type'] !== 'reference') continue;
                        $referenceRepository = $this->getEntityManager()->getRepository($reference['class']);
                        $referenceEntity = $referenceRepository->find($reference['id']);
                        if ($referenceEntity !== null) {
                            $unfrozenEntity->{$reference['setter']}($referenceEntity);
                            $reference['retrieval'] = 'entityManager';
                        }
                    }
                }
                $entity->{'set'.$freezableUpper}($unfrozenEntity);
            }
        }
    }
}
