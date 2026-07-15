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

class Events extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'events';

    protected function getPageParams(): array {
        $this->assertCanView();

        return [
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'LOAD_UPLOT'                    => true,
            'LOAD_DATATABLE'                => true,
            'LOAD_CHOICES'                  => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'HTML_FILE'                     => 'events.html',
            'JS'                            => 'events.js',
            'EVENT_TYPES'                   => sentrion('controllers')->events->getAllEventTypes(),
            'DEVICE_TYPES'                  => sentrion('controllers')->events->getAllDeviceTypes(),
            'RULES'                         => sentrion('controllers')->rules->getAllRulesByApiKey($this->apiKey),
            'INTERNAL_PAGE'                 => true,
        ];
    }

    public function getList(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getList($this->apiKey) : [];
    }

    public function getChart(): array {
        $this->assertCanView();

        $mode = sentrion('utils')->conversion->getStringRequestParam('mode');

        return $this->apiKey ? $this->controller->getChart($mode, $this->apiKey) : [];
    }

    public function getEventDetails(): array {
        $this->assertCanView();

        $result = [];

        if ($this->apiKey && $this->id) {
            $result = $this->controller->getEventDetails($this->id, $this->apiKey);
            if ($result) {
                $result = $this->controller->extendPayload($result, $this->apiKey);
            }
        }

        return $result;
    }
}
