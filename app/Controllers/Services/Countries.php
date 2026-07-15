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

class Countries extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        $result = sentrion('grids')->countries->getAll($apiKey);

        $ids = array_column($result['data'], 'id');
        if ($ids && sentrion('utils')->variables->getRecalculateTotalsOnVisit()) {
            sentrion('models')->country->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = sentrion('models')->country->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }

    public function getMap(int $apiKey): array {
        $map = [
            'userId'        => 'getCountriesByUserId',
            'ispId'         => 'getCountriesByIspId',
            'userAgentId'   => 'getCountriesByUserAgentId',
            'domainId'      => 'getCountriesByDomainId',
            'resourceId'    => 'getCountriesByResourceId',
        ];

        $result = $this->idMapIterate($map, sentrion('models')->map, $apiKey, 'getAllCountriesByDateRange');

        return $result;
    }

    public function getTimeFrameTotal(array $ids, string $startDate, string $endDate, int $apiKey): array {
        return [
            'SUCCESS_MESSAGE'   => sentrion('storage')->get('totals_success_message'),
            'totals'            => sentrion('models')->country->getTimeFrameTotal($ids, $startDate, $endDate, $apiKey),
        ];
    }
}
