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

class Main extends \Sentrion\Controllers\Services\Base {
    public function getCurrentTime(\Sentrion\Entities\Operator $operator): array {
        $offset = sentrion('utils')->timezones->getOperatorOffset($operator);
        $now = time() + $offset;
        $day = sentrion('utils')->constants->SECONDS_IN_DAY;
        $firstJan = mktime(0, 0, 0, 1, 1, intval(gmdate('Y')));

        $day = sentrion('utils')->conversion->intVal(ceil(($now - $firstJan) / $day), 0);

        return [
            'clock_offset'      => $offset,
            'clock_day'         => ($day < 10 ? '00' : ($day < 100 ? '0' : '')) . strval($day),
            'clock_time_his'    => date('H:i:s', $now),
            'clock_timezone'    => 'UTC' . (($offset < 0) ? '-' . date('H:i', -$offset) : '+' . date('H:i', $offset)),
        ];
    }

    public function getConstants(): array {
        $constants = sentrion('assets')->uiConstants->getConstantsObj();
        $constants = $constants::listConstants();

        return $constants ? $constants : [];
    }

    public function getSearchResults(?string $query, int $apiKey): array {
        $result = [];

        if ($query === '' || $query === null) {
            return ['suggestions' => $result];
        }

        $model = new \Sentrion\Models\Search\Domain();
        $result1 = $model->searchByDomain($query, $apiKey);

        $model = new \Sentrion\Models\Search\Ip();
        $result2 = $model->searchByIp($query, $apiKey);

        $model = new \Sentrion\Models\Search\Isp();
        $result3 = $model->searchByIsp($query, $apiKey);

        $model = new \Sentrion\Models\Search\User();
        $result4 = $model->searchByUserId($query, $apiKey);

        $model = new \Sentrion\Models\Search\Email();
        $result5 = $model->searchByEmail($query, $apiKey);

        $model = new \Sentrion\Models\Search\Phone();
        $result6 = $model->searchByPhone($query, $apiKey);

        $result = array_merge($result1, $result2, $result3, $result4, $result5, $result6);
        $iters = count($result);

        for ($i = 0; $i < $iters; ++$i) {
            $result[$i]['data'] = ['category' => $result[$i]['groupName']];
        }

        return [
            'suggestions' => $result,
        ];
    }
}
