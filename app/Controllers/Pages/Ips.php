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

class Ips extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'ips';

    protected function getPageParams(): array {
        $this->assertCanView();

        return [
            'LOAD_UPLOT'            => true,
            'LOAD_DATATABLE'        => true,
            'LOAD_CHOICES'          => true,
            'LOAD_AUTOCOMPLETE'     => true,
            'HTML_FILE'             => 'ips.html',
            'JS'                    => 'ips.js',
            'IP_TYPES'              => sentrion('utils')->constants->IP_TYPES,
            'INTERNAL_PAGE'         => true,
        ];
    }

    public function getList(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getList($this->apiKey) : [];
    }

    public function getChart(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getChart($this->apiKey) : [];
    }

    public function getTimeFrameTotal(): array {
        $this->assertCanView();

        if (!$this->apiKey) {
            return [];
        }

        $ids        = sentrion('utils')->conversion->getArrayRequestParam('ids');
        $startDate  = sentrion('utils')->conversion->getStringRequestParam('startDate');
        $endDate    = sentrion('utils')->conversion->getStringRequestParam('endDate');

        return $this->controller->getTimeFrameTotal($ids, $startDate, $endDate, $this->apiKey);
    }
}
