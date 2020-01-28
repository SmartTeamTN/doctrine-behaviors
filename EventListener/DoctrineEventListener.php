<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 * @todo find a better solution to access entityManager
 */

namespace SmartTeam\DoctrineBehaviors\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

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
     */
    public function postLoad(LifecycleEventArgs $args)
    {

    }
}
