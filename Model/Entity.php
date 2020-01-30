<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */

namespace SmartTeam\DoctrineBehaviors\Model;

use Doctrine\ORM\Proxy\Proxy;

/**
 * Trait Entity
 *
 * @package SmartTeam\DoctrineBehaviors\Model
 */
trait Entity
{
    public static function getClass($entity): string
    {
        $class = \is_string($entity) ? $entity : \get_class($entity);
        $reflection = new \ReflectionClass($class);
        if ($reflection->implementsInterface(Proxy::class)) {
            $class = $reflection->getParentClass()->getName();
        }

        return $class;
    }
}
