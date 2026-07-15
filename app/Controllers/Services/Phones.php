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

class Phones extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        $map = [
            'userId' => 'getPhonesByUserId',
        ];

        $result = $this->idMapIterate($map, sentrion('grids')->phones, $apiKey, null);

        $ids = array_column($result['data'], 'id');
        if ($ids && sentrion('utils')->variables->getRecalculateTotalsOnVisit()) {
            sentrion('models')->phone->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = sentrion('models')->phone->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }

    public function getChart(int $apiKey): array {
        return sentrion('charts')->phones->getData($apiKey);
    }

    public function getPhoneDetails(int $id, int $apiKey): array {
        $details = sentrion('models')->phone->getPhoneDetails($id, $apiKey);
        $details['enrichable'] = $this->isEnrichable($apiKey);

        $tsColumns = ['created', 'lastseen'];
        $details = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $details);

        return $details;
    }

    private function isEnrichable(int $apiKey): bool {
        return sentrion('models')->apiKeys->attributeIsEnrichable('phone', $apiKey);
    }

    public function enrichEntity(int $entityId, ?string $enrichmentKey, int $apiKey): array {
        if ($enrichmentKey === null) {
            return ['ERROR_CODE' => sentrion('utils')->errorCodes->ENRICHMENT_API_KEY_NOT_EXISTS];
        }
        set_error_handler([\Sentrion\Utils\ErrorHandler::class, 'exceptionErrorHandler']);
        $result = sentrion('controlles')->enrichment->enrichEntityProcess('phone', null, $entityId, $apiKey, $enrichmentKey);
        restore_error_handler();

        return $result;
    }
}
