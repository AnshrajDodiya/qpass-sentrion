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

class FieldAudits extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        $result = sentrion('grids')->fieldAudits->getAll($apiKey);

        $ids = array_column($result['data'], 'field_audit_id');
        if ($ids && sentrion('utils')->variables->getRecalculateTotalsOnVisit()) {
            sentrion('models')->fieldAudit->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = sentrion('models')->fieldAudit->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }

    public function getChart(int $apiKey): array {
        return sentrion('charts')->fields->getData($apiKey);
    }

    public function getTimeFrameTotal(array $ids, string $startDate, string $endDate, int $apiKey): array {
        return [
            'SUCCESS_MESSAGE'   => sentrion('storage')->get('totals_success_message'),
            'totals'            => sentrion('models')->fieldAudit->getTimeFrameTotal($ids, $startDate, $endDate, $apiKey),
        ];
    }

    public function getTrailList(int $apiKey): array {
        $map = [
            'userId'        => 'getDataByUserId',
            'resourceId'    => 'getDataByResourceId',
            'fieldId'       => 'getDataByFieldId',
        ];

        $result = $this->idMapIterate($map, sentrion('grids')->fieldAuditTrail, $apiKey);

        $ids = array_column($result['data'], 'field_audit_id');
        if ($ids && sentrion('utils')->variables->getRecalculateTotalsOnVisit()) {
            sentrion('models')->fieldAudit->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = sentrion('models')->fieldAudit->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }

    public function getFieldEventDetails(int $id, int $apiKey): array {
        $result = [];
        $trailResult = sentrion('models')->fieldAuditTrail->getById($id, $apiKey);

        if ($trailResult) {
            $eventId = $trailResult['event_id'];
            $result = sentrion('controllers')->events->getEventDetails($eventId, $apiKey);

            if ($result) {
                $result = sentrion('controllers')->events->extendPayload($result, $apiKey);
            }
        }

        return $result;
    }
}
