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

class Domain extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'domain';

    protected function proceedPostRequest(int $apiKey): array {
        $this->assertCanEdit();

        return match (sentrion('utils')->conversion->getStringRequestParam('cmd')) {
            'reenrichment' => sentrion('controllers')->enrichment->enrichEntityFromRequest($apiKey),
            default => []
        };
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        $domainId   = sentrion('utils')->conversion->getIntUrlParam('domainId');
        $hasAccess  = sentrion('controllers')->domain->checkIfOperatorHasAccess($domainId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest($this->apiKey) : [];

        $domain         = sentrion('controllers')->domain->getDomainDetails($domainId, $this->apiKey);
        $isEnrichable   = sentrion('controllers')->domain->isEnrichable($this->apiKey);
        $pageTitle      = sentrion('utils')->render->getInternalPageTitleWithPostfix($domain['domain']);

        $pageParams = [
            'LOAD_DATATABLE'                => true,
            'LOAD_JVECTORMAP'               => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'HTML_FILE'                     => 'domain.html',
            'DOMAIN'                        => $domain,
            'PAGE_TITLE'                    => $pageTitle,
            'LOAD_UPLOT'                    => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'JS'                            => 'domain.js',
            'IS_ENRICHABLE'                 => $isEnrichable,
            'INTERNAL_PAGE'                 => true,
        ];

        return array_merge($pageParams, $postParams);
    }

    public function getDomainDetails(): array {
        $this->assertCanView();

        $domainId = sentrion('utils')->conversion->getIntRequestParam('domainId');
        $hasAccess = $this->controller->checkIfOperatorHasAccess($domainId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        return $this->controller->getDomainDetails($domainId, $this->apiKey);
    }
}
