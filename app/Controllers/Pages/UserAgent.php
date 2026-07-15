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

class UserAgent extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'userAgent';

    public function proceedPostRequest(int $apiKey): array {
        $this->assertCanEdit();

        return match (sentrion('utils')->conversion->getStringRequestParam('cmd')) {
            'reenrichment' => sentrion('controllers')->enrichment->enrichEntity($apiKey),
            default => []
        };
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        $userAgentId    = sentrion('utils')->conversion->getIntUrlParam('userAgentId');
        $hasAccess      = sentrion('controllers')->userAgent->checkIfOperatorHasAccess($userAgentId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest($this->apiKey) : [];

        $userAgent      = sentrion('controllers')->userAgent->getUserAgentDetails($userAgentId, $this->apiKey);
        $pageTitle      = sentrion('utils')->render->getInternalPageTitleWithPostfix(strval($userAgent['id']));
        $isEnrichable   = sentrion('controllers')->userAgent->isEnrichable($this->apiKey);

        $pageParams = [
            'LOAD_DATATABLE'                => true,
            'LOAD_JVECTORMAP'               => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'HTML_FILE'                     => 'userAgent.html',
            'USER_AGENT'                    => $userAgent,
            'PAGE_TITLE'                    => $pageTitle,
            'LOAD_UPLOT'                    => true,
            'JS'                            => 'user_agent.js',
            'IS_ENRICHABLE'                 => $isEnrichable,
            'INTERNAL_PAGE'                 => true,
        ];

        return array_merge($pageParams, $postParams);
    }

    public function getUserAgentDetails(): array {
        $this->assertCanView();

        $userAgentId = sentrion('utils')->conversion->getIntRequestParam('userAgentId');
        $hasAccess = $this->controller->checkIfOperatorHasAccess($userAgentId, $this->apiKey);
        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        return $this->controller->getUserAgentDetails($userAgentId, $this->apiKey);
    }
}
