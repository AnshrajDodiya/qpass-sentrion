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

namespace Sentrion\Controllers\Services;

class Blacklist extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        return sentrion('grids')->blacklist->getAll($apiKey);
    }

    public function getChart(int $apiKey): array {
        return sentrion('charts')->blacklist->getData($apiKey);
    }

    public function removeItemFromBlacklist(int $itemId, string $type, int $apiKey): void {
        if (!in_array($type, ['ip', 'email', 'phone'])) {
            return;
        }

        sentrion('models')->$type->updateFraudFlag([$itemId], false, $apiKey);
    }

    public function setBlacklistUsersCount(bool $cache, int $apiKey): array {
        $operator = sentrion('utils')->routes->getCurrentRequestOperator();

        if ($operator->isGuest()) {
            $key = sentrion('entities')->apiKey->getById($apiKey);
            $operator = sentrion('entities')->operator->getById($key->creator);
        }

        $takeFromCache = $this->canTakeNumberOfBlacklistUsersFromCache($operator);

        $total = $operator->blacklistUsersCnt;
        if (!$cache || !$takeFromCache) {
            $total = sentrion('models')->dashboard->getTotalBlockedUsers(null, $apiKey);

            sentrion('models')->operator->updateBlacklistUsersCnt($total, $operator->id);
        }

        return ['total' => $total];
    }

    private function canTakeNumberOfBlacklistUsersFromCache(\Sentrion\Entities\Operator $operator): bool {
        $interval = sentrion('storage')->get('REVIEWED_QUEUE_CNT_CACHE_TIME');

        return !!sentrion('utils')->dateRange->inIntervalTillNow($operator->reviewQueueUpdatedAt, $interval);
    }
}
