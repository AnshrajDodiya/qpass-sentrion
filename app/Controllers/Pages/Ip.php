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

class Ip extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'ip';

    protected function proceedPostRequest(int $apiKey): array {
        $this->assertCanEdit();

        return match (sentrion('utils')->conversion->getStringRequestParam('cmd')) {
            'reenrichment' => sentrion('controllers')->enrichment->enrichEntityFromRequest($apiKey),
            default => []
        };
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        $ipId       = sentrion('utils')->conversion->getIntUrlParam('ipId');
        $hasAccess  = sentrion('controllers')->ip->checkIfOperatorHasAccess($ipId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest($this->apiKey) : [];

        $ipAddr         = sentrion('controllers')->ip->getFullIpInfoById($ipId, $this->apiKey);
        $pageTitle      = sentrion('utils')->render->getInternalPageTitleWithPostfix($ipAddr['ip']);
        $isEnrichable   = sentrion('controllers')->ip->isEnrichable($this->apiKey);

        $pageParams = [
            'LOAD_DATATABLE'                => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'HTML_FILE'                     => 'ip.html',
            'PAGE_TITLE'                    => $pageTitle,
            'IP'                            => $ipAddr,
            'LOAD_UPLOT'                    => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'JS'                            => 'ip.js',
            'IS_ENRICHABLE'                 => $isEnrichable,
            'INTERNAL_PAGE'                 => true,
        ];

        return array_merge($pageParams, $postParams);
    }

    public function getIpDetails(): array {
        $this->assertCanView();

        $ipId = sentrion('utils')->conversion->getIntRequestParam('ipId');
        $hasAccess = $this->controller->checkIfOperatorHasAccess($ipId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        return $this->controller->getIpDetails($ipId, $this->apiKey);
    }
}
