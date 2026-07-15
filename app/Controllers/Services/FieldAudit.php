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

class FieldAudit extends \Sentrion\Controllers\Services\Base {
    public function checkIfOperatorHasAccess(int $fieldId, int $apiKey): bool {
        return sentrion('models')->fieldAudit->checkAccess($fieldId, $apiKey);
    }

    public function getFieldById(int $fieldId, int $apiKey): array {
        $result = sentrion('models')->fieldAudit->getFieldById($fieldId, $apiKey);
        $result['lastseen'] = sentrion('utils')->elapsedDate->short($result['lastseen']);
        $result['created'] = sentrion('utils')->elapsedDate->short($result['created']);

        return $result;
    }
}
