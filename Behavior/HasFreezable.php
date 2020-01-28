<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 * @see https://devpmeti.atlassian.net/browse/PAYM-105
 */

namespace SmartTeam\DoctrineBehaviors\Behavior;

use SmartTeam\DoctrineBehaviors\Model\Freezable as FreezableModel;
use Datetime;
use DateTimeZone;
use Exception;
use SmartTeam\DoctrineBehaviors\Model\Entity as EntityModel;

/**
 * Trait HasFreezable
 * @todo make this in config
 *
 * @package SmartTeam\DoctrineBehaviors\Behavior
 */
trait HasFreezable
{
    /**
     * @return array|null
     */
    public function getFreezables(): ?array
    {
        return isset($this->_freezables) && is_array($this->_freezables) ? $this->_freezables : [];
    }
}
