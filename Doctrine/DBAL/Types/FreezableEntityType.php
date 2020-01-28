<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */

namespace SmartTeam\DoctrineBehaviors\Doctrine\DBAL\Types;

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

        return $value->freezeState();
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

        unset($doctrineEventListener);
        unset($entityRepository);

        $unfrozenEntity = new $metadata['class']();
        return $unfrozenEntity->prepareToUnfreeze($metadata);
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
