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

namespace Sentrion\Models\Grid\FieldAuditTrail;

class Grid extends \Sentrion\Models\Grid\Base\Grid {
    public function getDataByUserId(int $userId, int $apiKey): array {
        $params = [':account_id' => $userId];

        return $this->getGrid($apiKey, $this->idsModel->getDataIdsByUserId(), $params);
    }

    public function getDataByFieldId(int $fieldId, int $apiKey): array {
        $params = [':field_id' => $fieldId];

        return $this->getGrid($apiKey, $this->idsModel->getDataIdsByFieldId(), $params);
    }

    public function getDataByResourceId(int $resourceId, int $apiKey): array {
        $params = [':resource_id' => $resourceId];

        return $this->getGrid($apiKey, $this->idsModel->getDataIdsByResourceId(), $params);
    }

    public function getAll(int $apiKey): array {
        return $this->getGrid($apiKey);
    }

    protected function convertTimeToUserTimezone(array &$result): void {
        $fields = ['created'];

        $result = sentrion('utils')->timezones->translateTimezones($result, $fields);
    }
}
