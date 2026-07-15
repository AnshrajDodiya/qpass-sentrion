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

namespace Sentrion\Models\Grid\Devices;

class Grid extends \Sentrion\Models\Grid\Base\Grid {
    public function getDevicesByIpId(int $ipId, int $apiKey): array {
        $params = [':ip_id' => $ipId];

        return $this->getGrid($apiKey, $this->idsModel->getDevicesIdsByIpId(), $params);
    }

    public function getDevicesByUserId(int $userId, int $apiKey): array {
        $params = [':account_id' => $userId];

        return $this->getGrid($apiKey, $this->idsModel->getDevicesIdsByUserId(), $params);
    }

    public function getDevicesByResourceId(int $resourceId, int $apiKey): array {
        $params = [':resource_id' => $resourceId];

        return $this->getGrid($apiKey, $this->idsModel->getDevicesIdsByResourceId(), $params);
    }

    public function getAll(int $apiKey): array {
        return $this->getGrid($apiKey);
    }

    protected function calculateCustomParams(array &$result): void {
        $result = sentrion('utils')->enrichment->applyDeviceParams($result);
    }

    protected function convertTimeToUserTimezone(array &$result): void {
        $fields = ['created'];

        $result = sentrion('utils')->timezones->translateTimezones($result, $fields);
    }
}
