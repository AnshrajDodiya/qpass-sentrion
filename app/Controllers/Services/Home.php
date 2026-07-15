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

class Home extends \Sentrion\Controllers\Services\Base {
    public function getStat(string $mode, ?array $dateRange, int $apiKey): array {
        $result = [
            'total'         => null,
            'allTimeTotal'  => null,
        ];

        // NOTE: removed allTimeTotal key
        switch ($mode) {
            case 'totalEvents':
                $result['total'] = sentrion('models')->dashboard->getTotalEvents($dateRange, $apiKey);
                break;
            case 'totalUsers':
                $result['total'] = sentrion('models')->dashboard->getTotalUsers($dateRange, $apiKey);
                break;
            case 'totalIps':
                $result['total'] = sentrion('models')->dashboard->getTotalIps($dateRange, $apiKey);
                break;
            case 'totalCountries':
                $result['total'] = sentrion('models')->dashboard->getTotalCountries($dateRange, $apiKey);
                break;
            case 'totalUrls':
                $result['total'] = sentrion('models')->dashboard->getTotalResources($dateRange, $apiKey);
                break;
            case 'totalUsersForReview':
                $result['total'] = sentrion('models')->dashboard->getTotalUsersForReview($dateRange, $apiKey);
                break;
            case 'totalBlockedUsers':
                $result['total'] = sentrion('models')->dashboard->getTotalBlockedUsers($dateRange, $apiKey);
                break;
        }

        return $result;
    }

    public function getTopTen(string $mode, ?array $dateRange, int $apiKey): array {
        $modelMap = sentrion('utils')->constants->TOP_TEN_MODELS_MAP;

        $model = array_key_exists($mode, $modelMap) ? new $modelMap[$mode]() : null;
        $data = $model ? $model->getList($apiKey, $dateRange) : [];
        $total = count($data);

        return [
            'draw'              => sentrion('request')->getRequestParam('draw') ?? 1,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $total,
            'data'              => $data,
        ];
    }
}
