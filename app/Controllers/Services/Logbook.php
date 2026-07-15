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

class Logbook extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        return sentrion('grids')->logbook->getAll($apiKey);
    }

    public function getChart(int $apiKey): array {
        return sentrion('charts')->logbook->getData($apiKey);
    }

    public function getLogbookDetails(int $id, int $apiKey): array {
        $result = sentrion('models')->logbook->getLogbookDetails($id, $apiKey);

        $tsColumns = ['started', 'ended'];
        $result = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $result);

        return $result;
    }
}
