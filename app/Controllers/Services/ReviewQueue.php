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

class ReviewQueue extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        return sentrion('grids')->reviewQueue->getAll($apiKey);
    }

    public function getChart(int $apiKey): array {
        return sentrion('charts')->reviewQueue->getData($apiKey);
    }

    public function setNotReviewedCount(bool $cache, int $apiKey): array {
        $operator = sentrion('utils')->routes->getCurrentRequestOperator();

        if ($operator->isGuest()) {
            $key = sentrion('entities')->apiKey->getById($apiKey);
            $operator = sentrion('entities')->operator->getById($key->creator);
        }

        $takeFromCache = $this->canTakeNumberOfNotReviewedUsersFromCache($operator);

        $total = $operator->reviewQueueCnt;
        if (!$cache || !$takeFromCache) {
            $total = sentrion('models')->reviewQueue->getCount($apiKey);

            sentrion('models')->operator->updateReviewedQueueCnt($total, $operator->id);
        }

        return ['total' => $total];
    }

    private function canTakeNumberOfNotReviewedUsersFromCache(\Sentrion\Entities\Operator $operator): bool {
        $interval = sentrion('storage')->get('REVIEWED_QUEUE_CNT_CACHE_TIME');

        return !!sentrion('utils')->dateRange->inIntervalTillNow($operator->reviewQueueUpdatedAt, $interval);
    }

    public function addToBlacklist(int $userId, int $apiKey): false|int {
        if (!sentrion('controllers')->user->checkIfOperatorHasAccess($userId, $apiKey)) {
            return false;
        }

        sentrion('controllers')->user->addToBlacklistQueue($userId, true, false, true, $apiKey);   // recalculate

        return sentrion('utils')->errorCodes->USER_FRAUD_FLAG_SET;
    }

    public function addToWhitelist(int $userId, int $apiKey): false|int {
        if (!sentrion('controllers')->user->checkIfOperatorHasAccess($userId, $apiKey)) {
            return false;
        }

        sentrion('controllers')->user->addToBlacklistQueue($userId, false, false, true, $apiKey);   // recalculate

        return sentrion('utils')->errorCodes->USER_FRAUD_FLAG_UNSET;
    }
}
