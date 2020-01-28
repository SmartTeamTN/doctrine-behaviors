<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */

namespace SmartTeam\DoctrineBehaviors\Doctrine\DBAL\Types;

use SmartTeam\DoctrineBehaviors\EventListener\DoctrineEventListener;
use SmartTeam\DoctrineBehaviors\Model\Entity as EntityModel;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use SmartTeam\DoctrineBehaviors\Model\Freezable as FreezableModel;
use Exception;

/**
 * Class FreezableEntityType
 *
 * @package SmartTeam\DoctrineBehaviors\Doctrine\DBAL\Types
 */
final class FreezableEntityType extends Type
{
    public const FREEZABLE_ENTITY = 'freezable_entity';

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'LONGTEXT';
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return array|mixed|null
     *
     * @throws Exception
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) return null;

        $freezable = FreezableModel::isFreezable($value);
        if (!$freezable['status']) {
            throw new Exception($freezable['message']);
        }
        unset($freezable);

        /**
         * @var $doctrineEventListener DoctrineEventListener
         */
        $doctrineEventListener = $platform->getEventManager()->getListeners('postLoad')['_service_SmartTeam\DoctrineBehaviors\EventListener\DoctrineEventListener'];
        $entityManager = $doctrineEventListener->getEntityManager();

        try {
            $entityRepository = $entityManager->getRepository(EntityModel::getClass($value));
        } catch (Exception $exception) {
            return null;
        }

        try {
            $entity = $entityRepository->find($value->getId()->toString());
        } catch (Exception $exception) {
            $entity = $value;
        }

        unset($doctrineEventListener);
        unset($entityManager);
        unset($entityRepository);

        return $entity->freezeState();
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|null
     *
     * @throws Exception
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) return null;

        $metadata = json_decode($value, true);
        if (empty($metadata['class'])) return null;
        $freezable = FreezableModel::isFreezable($metadata['class']);
        if (!$freezable['status']) {
            throw new Exception($freezable['message']);
        }

        /**
         * @var $doctrineEventListener DoctrineEventListener
         */
        $doctrineEventListener = $platform->getEventManager()->getListeners('postLoad')['_service_SmartTeam\DoctrineBehaviors\EventListener\DoctrineEventListener'];

        $entityManager = $doctrineEventListener->getEntityManager();

        try {
            $entityRepository = $entityManager->getRepository($metadata['class']);
        } catch (Exception $exception) {
            return null;
        }

        try {
            $currentEntity = $entityRepository->find($metadata['data']['id']);
        } catch (Exception $exception) {

        }

        unset($doctrineEventListener);
        unset($entityRepository);

        $unfrozenEntity = new $metadata['class']();
        $metadata['references'] = [];

        $unfrozenEntity = $unfrozenEntity->unfreezeState($metadata, $currentEntity ?? null);
        if (!empty($metadata['references'])) {
            foreach ($metadata['references'] as &$reference) {
                if (isset($reference['retrieval'])) continue;
                if (!isset($reference['type']) || $reference['type'] !== 'reference') continue;
                $referenceRepository = $entityManager->getRepository($reference['class']);
                $referenceEntity = $referenceRepository->find($reference['id']);
                if ($referenceEntity !== null) {
                    $unfrozenEntity->{$reference['setter']}($referenceEntity);
                    $reference['retrieval'] = 'entityManager';
                }
            }
        }
        return $unfrozenEntity;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::FREEZABLE_ENTITY;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
