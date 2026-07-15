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

class Domain extends \Sentrion\Controllers\Services\Base {
    public function checkIfOperatorHasAccess(int $domainId, int $apiKey): bool {
        return sentrion('models')->domain->checkAccess($domainId, $apiKey);
    }

    public function getDomainDetails(int $domainId, int $apiKey): array {
        $result = sentrion('models')->domain->getFullDomainInfoById($domainId, $apiKey);

        $tsColumns = ['lastseen'];
        $result = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $result);

        return $result;
    }

    public function isEnrichable(int $apiKey): bool {
        return sentrion('models')->apiKeys->attributeIsEnrichable('domain', $apiKey);
    }
}
