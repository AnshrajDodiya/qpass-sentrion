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

class Isp extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'isp';

    protected function getPageParams(): array {
        $this->assertCanView();

        $ispId = sentrion('utils')->conversion->getIntUrlParam('ispId');
        $hasAccess = sentrion('controllers')->isp->checkIfOperatorHasAccess($ispId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $isp = sentrion('controllers')->isp->getFullIspInfoById($ispId, $this->apiKey);
        $pageTitle = sentrion('utils')->render->getInternalPageTitleWithPostfix(strval($isp['asn']));

        return [
            'LOAD_DATATABLE'                => true,
            'LOAD_JVECTORMAP'               => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'HTML_FILE'                     => 'isp.html',
            'ISP'                           => $isp,
            'PAGE_TITLE'                    => $pageTitle,
            'LOAD_UPLOT'                    => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'JS'                            => 'isp.js',
            'INTERNAL_PAGE'                 => true,
        ];
    }

    public function getIspDetails(): array {
        $this->assertCanView();

        $ispId = sentrion('utils')->conversion->getIntRequestParam('ispId');
        $hasAccess = $this->controller->checkIfOperatorHasAccess($ispId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        return $this->controller->getIspDetails($ispId, $this->apiKey);
    }
}
