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

class Users extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'users';

    protected function getPageParams(): array {
        $this->assertCanView();

        $ruleUid = sentrion('utils')->conversion->getStringRequestParam('ruleUid');
        $ruleUid = $ruleUid ? strtoupper($ruleUid) : null;

        return [
            'LOAD_UPLOT'            => true,
            'LOAD_DATATABLE'        => true,
            'LOAD_AUTOCOMPLETE'     => true,
            'LOAD_CHOICES'          => true,
            'HTML_FILE'             => 'users.html',
            'JS'                    => 'users.js',
            'RULES'                 => sentrion('controllers')->rules->getAllRulesByApiKey($this->apiKey),
            'DEFAULT_RULE'          => $ruleUid,
            'INTERNAL_PAGE'         => true,
        ];
    }

    public function getChart(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getChart($this->apiKey) : [];
    }

    public function getList(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getList($this->apiKey) : [];
    }
}
