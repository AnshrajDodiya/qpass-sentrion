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

class Home extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'home';

    protected function authPage(): void {
        sentrion('response')->redirectNotLoggedIn('/login');
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        return [
            'LOAD_DATATABLE'    => true,
            'LOAD_AUTOCOMPLETE' => true,
            'HTML_FILE'         => 'home.html',
            'JS'                => 'dashboard.js',
            'INTERNAL_PAGE'     => true,
        ];
    }

    public function getDashboardStat(): array {
        $this->assertCanView();

        $mode = sentrion('utils')->conversion->getStringRequestParam('mode');
        $dateRange = sentrion('utils')->dateRange->getDatesRangeFromRequest();

        return $this->apiKey ? $this->controller->getStat($mode, $dateRange, $this->apiKey) : [];
    }

    public function getTopTen(): array {
        $this->assertCanView();

        $mode = sentrion('utils')->conversion->getStringRequestParam('mode');
        $dateRange = sentrion('utils')->dateRange->getDatesRangeFromRequest();

        return $this->apiKey ? $this->controller->getTopTen($mode, $dateRange, $this->apiKey) : [];
    }
}
