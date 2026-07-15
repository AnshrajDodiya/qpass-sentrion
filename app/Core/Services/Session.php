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

namespace Sentrion\Core\Services;

class Session {
    public function getCurrentOperator(): \Sentrion\Entities\Operator {
        return sentrion('utils')->routes->getCurrentRequestOperator();
    }

    public function getCurrentKey(): ?\Sentrion\Entities\ApiKey {
        return sentrion('utils')->routes->getCurrentRequestApiKey();
    }

    public function extractCurrentOperator(): void {
        sentrion('utils')->routes->setCurrentRequestOperator();
        sentrion('utils')->routes->setCurrentRequestApiKey();
    }

    public function setActiveOperator(int $operatorId): void {
        $this->set('active_user_id', $operatorId);
    }

    public function get(string $key): mixed {
        return sentrion('storage')->get('SESSION.' . $key);
    }

    // WARN: $value should be serializable
    public function set(string $key, mixed $value): mixed {
        return sentrion('storage')->set('SESSION.' . $key, $value);
    }

    public function remove(string $key): mixed {
        return sentrion('storage')->remove('SESSION.' . $key);
    }

    public function clear(): void {
        sentrion('storage')->remove('SESSION');
    }
}
