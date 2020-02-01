<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 * @version 0.1
 */

namespace SmartTeam\DoctrineBehaviors\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * use with beberlei/doctrineextensions package
 * Trait FreezableSearch
 *
 * @package SmartTeam\DoctrineBehaviors\Traits
 */
Trait FreezableSearch
{
    /**
     * @param QueryBuilder|null $queryBuilder
     * @param array|null $params
     *
     * @return QueryBuilder|null
     */
    public static function search(?QueryBuilder &$queryBuilder, ?array $params): ?QueryBuilder
    {
        if (empty($params) || !is_array($params) || $queryBuilder === null) return $queryBuilder;

        foreach ($params['filters'] as $filter) {
            if (empty($filter['field']) || empty($filter['frozenField']) || empty($filter['data'])) {
                // define what to do
                // @todo
            }
            $queryBuilder->{$filter['method'] ?? 'andWhere'}('REGEXP(' . ($filter['entityIdentifier'] ? $filter['entityIdentifier'] . '.' : '') . $filter['field'] . ', :' . $filter['placeholder'] . '_REGEX) = true')
                ->setParameter($filter['placeholder'] . '_REGEX', '"' . $filter['frozenField'] . '":"' . $filter['data'] . '"');
            if ($filter['class']) {
                $queryBuilder->{$filter['method'] ?? 'andWhere'}('REGEXP(' . ($filter['entityIdentifier'] ? $filter['entityIdentifier'] . '.' : '') . $filter['field'] . ', :' . $filter['placeholder'] . '_CLASS_REGEX) = true')
                    ->setParameter($filter['placeholder'] . '_CLASS_REGEX', '"class":"' . str_replace('\\', '\\\\\\\\', $filter['class']) . '"');
            }
        }
        return $queryBuilder;
    }
}
