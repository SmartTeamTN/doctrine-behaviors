<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */

namespace SmartTeam\DoctrineBehaviors\Model;

use SmartTeam\DoctrineBehaviors\Model\Entity as EntityModel;
use ReflectionClass;
use ReflectionException;

/**
 * Trait HasFreezable
 *
 * @package SmartTeam\DoctrineBehaviors\Model
 */
trait HasFreezable
{
    /**
     * @param object $entity
     *
     * @return array
     */
    public static function hasFreezable($entity): array
    {
        if (is_object($entity)) {
            $class = get_class($entity);
        } else {
            $class = $entity;
        }
        $class = EntityModel::getClass($class);
        try {
            $refectionClass = new ReflectionClass($class);
        } catch (ReflectionException $reflectionException) {
            return [
                'status' => false,
                'message' => $reflectionException->getMessage(),
            ];
        }

        if (empty($refectionClass->getTraits()[\SmartTeam\DoctrineBehaviors\Behavior\HasFreezable::class])) {
            return [
                'status' => false,
                'message' => "Class {$class} does not use HasFreezable trait",
            ];
        }

        return [
            'status' => true,
        ];
    }
}
