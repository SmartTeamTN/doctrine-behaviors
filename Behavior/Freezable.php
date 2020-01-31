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
 * Trait Freezable
 *
 * @package SmartTeam\DoctrineBehaviors\Behavior
 */
trait Freezable
{
    public static $FROZEN_DATE_FORMAT = 'd/m/Y H:i:s';

    /**
     * @var bool|null
     */
    protected $frozen = null;

    /**
     * @var Datetime|null
     */
    protected $frozenAt = null;

    /**
     * @var object|null
     */
    protected $masterEntity = null;

    /**
     * @var array|null
     */
    protected $metadata = null;

    /**
     * @return Datetime|null
     */
    public function getFrozenAt(): ?Datetime
    {
        return $this->frozenAt;
    }

    /**
     * @return bool
     */
    public function isFrozen(): bool
    {
        return !is_bool($this->frozen) ? false : $this->frozen;
    }

    /**
     * @return object|null
     */
    public function getMasterEntity(): ?object
    {
        return $this->masterEntity;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return is_array($this->metadata) ? $this->metadata : [];
    }

    /**
     * freeze current state of entity
     *
     * @return string
     *
     * @throws Exception
     */
    public function freezeState(): string
    {
        $freezable = FreezableModel::isFreezable($this);
        if (!$freezable['status']) {
            throw new Exception($freezable['message']);
        }
        unset($freezable);
        if (!$this->isFrozen()) {
            $dateTime = (DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))))->setTimezone(new DateTimeZone(date_default_timezone_get()));

            $this->frozen = true;
            $this->frozenAt = $dateTime;
            $this->metadata = [
                'class' => EntityModel::getClass($this),
                'data' => $this->toJson(),
                'datetime' => $dateTime->format(self::$FROZEN_DATE_FORMAT),
            ];
        }

        return json_encode($this->getMetadata());
    }

    /**
     * unfreeze current state of entity
     *
     * @param array $metadata
     * @param object|null $current
     *
     * @return self
     *
     * @throws Exception
     */
    public function unfreezeState(?array &$metadata, ?object $current = null): self
    {
        $freezable = FreezableModel::isFreezable($this);
        if (!$freezable['status']) {
            throw new Exception($freezable['message']);
        }
        unset($freezable);
        if (empty($metadata)) return $this;

        $frozenAt = Datetime::createFromFormat(self::$FROZEN_DATE_FORMAT, $metadata['datetime']);

        $this->createFromFrozen($metadata, $current);

        $this->frozenAt = $frozenAt;
        $this->frozen = true;
        $this->masterEntity = $current;
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @param array|null $metadata
     *
     * @return self
     *
     * @throws Exception
     */
    public function prepareToUnfreezeState(?array $metadata = []): self
    {
        $freezable = FreezableModel::isFreezable($this);
        if (!$freezable['status']) {
            throw new Exception($freezable['message']);
        }
        unset($freezable);
        if (!empty($metadata) && isset($metadata['class']) && $metadata['class'] === EntityModel::getClass($this) && isset($metadata['data'])) {
            $this->metadata = $metadata;
            $this->frozen = true;
        }
        return $this;
    }
}
