<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */

namespace SmartTeam\DoctrineBehaviors\Model;

/**
 * Trait Entity
 *
 * @package SmartTeam\DoctrineBehaviors\Model
 */
trait Entity
{
    /**
     * @param $entity
     *
     * @return string
     */
    public static function getClass($entity): string
    {
        if (is_object($entity)) {
            $class = get_class($entity);
        } else {
            $class = $entity;
        }
        return str_replace('Proxies\\__CG__\\', '', $class);
    }
}
