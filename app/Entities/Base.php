<?php

/**
 * sentrion ~ open-source security framework
 * Copyright (c) Sentrion Technologies Sàrl (https://www.sentrion.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Sentrion Technologies Sàrl (https://www.sentrion.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.sentrion.com Sentrion(tm)
 */

declare(strict_types=1);

namespace Sentrion\Entities;

// NOTE: has nested entities
abstract class Base {
    protected array $tsFields;

    protected array $nestedProps;

    protected bool $modifiedObject = false;
    protected bool $localizedTs = false;

    public function __set(string $name, mixed $value): void {
        if ($name === 'id' || $name === 'scoreDetails' || in_array($name, $this->tsFields)) {
            throw new \Exception('Modification not allowed for property ' . $name);
        }

        if (!property_exists($this, $name)) {
            throw new \Exception('Unknown property ' . $name);
        }

        $this->$name = $value;
        $this->modifiedObject = true;
    }

    public function __get(string $name): mixed {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \Exception('Unknown property ' . $name);
    }

    public function hasUnsavedChanges(): bool {
        return $this->modifiedObject;
    }

    public function localizeTimestamps(?string $timezone = null): void {
        $dateTimezone = sentrion('utils')->timezones->getTimezone($timezone ?? sentrion('session')->getCurrentOperator()?->timezone);
        $utc = sentrion('utils')->timezones->getUtcTimezone();

        foreach ($this->tsFields as &$prop) {
            if (property_exists($this, $prop) && $this->$prop) {
                $this->$prop = sentrion('utils')->timezones->localizeTimestamp($this->$prop, $utc, $dateTimezone, false);
            }
        }

        unset($prop);

        foreach ($this->nestedProps as &$prop) {
            if (property_exists($this, $prop) && $this->$prop) {
                $this->$prop->localizeTimestamps($timezone);
            }
        }

        unset($prop);

        $this->localizedTs = true;
    }
}
