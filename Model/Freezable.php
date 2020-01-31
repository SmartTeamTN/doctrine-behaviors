<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */

namespace SmartTeam\DoctrineBehaviors\Model;

use SmartTeam\DoctrineBehaviors\Model\Entity as EntityModel;
use ReflectionClass;
use ReflectionException;

/**
 * Trait Freezable
 * 
 * @package SmartTeam\DoctrineBehaviors\Model
 */
trait Freezable
{
    /**
     * @param object $entity
     *
     * @return array
     */
    public static function isFreezable($entity): array
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

        if (empty($refectionClass->getTraits()[\SmartTeam\DoctrineBehaviors\Behavior\Freezable::class])) {
            return [
                'status' => false,
                'message' => "Class {$class} does not use Freezable trait",
            ];
        }

        try {
            $refectionClass->getMethod('toJson');
        } catch (ReflectionException $reflectionException) {
            return [
                'status' => false,
                'message' => $reflectionException->getMessage(),
            ];
        }

        try {
            $refectionClass->getMethod('createFromFrozen');
        } catch (ReflectionException $reflectionException) {
            return [
                'status' => false,
                'message' => $reflectionException->getMessage(),
            ];
        }

        try {
            $refectionClass->getMethod('prepareToUnfreezeState');
        } catch (ReflectionException $reflectionException) {
            return [
                'status' => false,
                'message' => $reflectionException->getMessage(),
            ];
        }

        return [
            'status' => true,
        ];
    }
}
