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

class Blacklist extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'blacklist';

    protected function getPageParams(): array {
        $this->assertCanView();

        return [
            'LOAD_UPLOT'            => true,
            'LOAD_DATATABLE'        => true,
            'LOAD_CHOICES'          => true,
            'LOAD_AUTOCOMPLETE'     => true,
            'HTML_FILE'             => 'blacklist.html',
            'JS'                    => 'blacklist.js',
            'ENTITY_TYPES'          => sentrion('utils')->constants->ENTITY_TYPES,
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

    public function setBlacklistUsersCount(bool $cache = false): array {
        $this->assertCanEdit();

        return $this->apiKey ? $this->controller->setBlacklistUsersCount($cache, $this->apiKey) : [];
    }

    public function removeFromBlacklist(): array {
        $this->assertCanDelete();

        if (!$this->apiKey || !$this->id) {
            return [];
        }

        $type   = sentrion('utils')->conversion->getStringRequestParam('type');
        $this->controller->removeItemFromBlacklist($this->id, $type, $this->apiKey);
        $successCode = sentrion('utils')->errorCodes->ITEM_REMOVED_FROM_BLACKLIST;

        return [
            'success'   => $successCode,
            'id'        => $this->id,
        ];
    }
}
