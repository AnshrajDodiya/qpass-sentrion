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

class Devices extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        $map = [
            'ipId'          => 'getDevicesByIpId',
            'userId'        => 'getDevicesByUserId',
            'resourceId'    => 'getDevicesByResourceId',
        ];

        return $this->idMapIterate($map, sentrion('grids')->devices, $apiKey);
    }

    public function getChart(int $apiKey): array {
        return sentrion('charts')->devices->getData($apiKey);
    }

    public function getDeviceDetails(int $id, int $apiKey): array {
        $details = sentrion('models')->device->getFullDeviceInfoById($id, $apiKey);
        $details['enrichable'] = $this->isEnrichable($apiKey);

        $tsColumns = ['created'];
        $details = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $details);

        return $details;
    }

    private function isEnrichable(int $apiKey): bool {
        return sentrion('models')->apiKeys->attributeIsEnrichable('ua', $apiKey);
    }
}
