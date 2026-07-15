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

class Rules {
    public array $rules;
    public int $key;

    private array $tsFields = ['updated', 'created', 'proportionUpdated'];

    public function __construct(
        array $rules,
        int $key,
    ) {
        $this->rules    = $rules;
        $this->key      = $key;
    }

    public static function getAll(int $key): ?self {
        $result = sentrion('models')->rules->getRulesByOperator($key);

        $preparedRules = [];

        foreach ($result as $rule) {
            $preparedRules[] = self::fillEntity($rule, $key);
        }

        return new self($preparedRules, $key);
    }

    public static function getByUserId(int $userId, int $key): ?self {
        $result = sentrion('models')->rules->getRulesByUserId($userId, $key);

        $preparedRules = [];

        foreach ($result as $rule) {
            $preparedRules[] = self::fillEntity($rule, $key);
        }

        return new self($preparedRules, $key);
    }

    private static function fillEntity(array $data, int $key): \Sentrion\Entities\Rule {
        return sentrion('entities')->rule->getFromQuery($data, $key);
    }

    public function localizeTimestamps(?string $timezone = null): void {
        $timezone = sentrion('utils')->timezones->getTimezone($timezone ?? sentrion('session')->getCurrentOperator()?->timezone);
        $utc = sentrion('utils')->timezones->getUtcTimezone();

        foreach ($this->rules as &$rule) {
            foreach ($this->tsFields as $prop) {
                $rule[$prop] = sentrion('utils')->timezones->localizeTimestamp($rule[$prop], $utc, $timezone, false);
            }
        }

        unset($rule);
    }
}
