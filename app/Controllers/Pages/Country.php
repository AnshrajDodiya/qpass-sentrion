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

class Country extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'country';

    protected function getPageParams(): array {
        $this->assertCanView();

        $countryId = sentrion('utils')->conversion->getIntUrlParam('countryId');
        $hasAccess = sentrion('controllers')->country->checkIfOperatorHasAccess($countryId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $country = sentrion('controllers')->country->getCountryById($countryId, $this->apiKey);
        $pageTitle = sentrion('utils')->render->getInternalPageTitleWithPostfix($country['name']);

        return [
            'LOAD_DATATABLE'                => true,
            'LOAD_UPLOT'                    => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'HTML_FILE'                     => 'country.html',
            'COUNTRY'                       => $country,
            'PAGE_TITLE'                    => $pageTitle,
            'JS'                            => 'country.js',
            'INTERNAL_PAGE'                 => true,
        ];
    }
}
