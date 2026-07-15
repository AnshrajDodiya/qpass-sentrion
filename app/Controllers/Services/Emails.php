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

class Emails extends \Sentrion\Controllers\Services\Base {
    public function getList(int $apiKey): array {
        $map = [
            'userId' => 'getEmailsByUserId',
        ];

        return $this->idMapIterate($map, sentrion('grids')->emails, $apiKey, null);
    }

    public function getEmailDetails(int $id, int $apiKey): array {
        $details = sentrion('models')->email->getEmailDetails($id, $apiKey);
        $details['enrichable'] = $this->isEnrichable($apiKey);

        $tsColumns = ['email_created', 'email_lastseen', 'domain_lastseen', 'domain_created'];
        $details = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $details);

        return $details;
    }

    private function isEnrichable(int $apiKey): bool {
        return sentrion('models')->apiKeys->attributeIsEnrichable('email', $apiKey);
    }

    public function enrichEntity(int $entityId, ?string $enrichmentKey, int $apiKey): array {
        if ($enrichmentKey === null) {
            return ['ERROR_CODE' => sentrion('utils')->errorCodes->ENRICHMENT_API_KEY_NOT_EXISTS];
        }
        set_error_handler([\Sentrion\Utils\ErrorHandler::class, 'exceptionErrorHandler']);
        $result = sentrion('controlles')->enrichment->enrichEntityProcess('email', null, $entityId, $apiKey, $enrichmentKey);
        restore_error_handler();

        return $result;
    }
}
