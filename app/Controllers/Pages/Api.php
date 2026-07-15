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

namespace Sentrion\Controllers\Pages;

class Api extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'api';

    protected function proceedPostRequest(int $apiKey): array {
        $this->assertCanEdit();

        return match (sentrion('utils')->conversion->getStringRequestParam('cmd')) {
            'resetKey'          => sentrion('controllers')->api->resetApiKey($apiKey),
            'updateApiUsage'    => sentrion('controllers')->api->updateApiUsage($apiKey),
            'enrichAll'         => sentrion('controllers')->api->enrichAll($apiKey),
            default => []
        };
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest($this->apiKey) : [];

        $scheduledForEnrichment = sentrion('controllers')->api->getScheduledForEnrichment($this->apiKey);
        [$isOwner, $apiKeys] = sentrion('controllers')->api->getOperatorApiKeysDetails($this->operator->id);

        $pageParams = [
            'LOAD_AUTOCOMPLETE'         => true,
            'LOAD_DATATABLE'            => true,
            'HTML_FILE'                 => 'api.html',
            'JS'                        => 'api.js',
            'API_URL'                   => sentrion('utils')->variables->getHostWithProtocolAndBase() . '/sensor/',
            'NOT_CHECKED'               => sentrion('controllers')->api->getNotCheckedEntities($this->apiKey),
            'SCHEDULED_FOR_ENRICHMENT'  => $scheduledForEnrichment,
            'IS_OWNER'                  => $isOwner,
            'API_KEYS'                  => $apiKeys,
            'INTERNAL_PAGE'             => true,
        ];

        return array_merge($pageParams, $postParams);
    }

    public function getUsageStats(): array {
        $this->assertCanView();

        return $this->operator->isLoggedIn() ? $this->controller->getUsageStats($this->operator->id) : [];
    }

    public function getNotCheckedEntitiesCount(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getNotCheckedEntitiesCount($this->apiKey) : [];
    }
}
