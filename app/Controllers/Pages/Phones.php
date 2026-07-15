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

class Phones extends \Sentrion\Controllers\Pages\Base {
    protected string $page = 'phones';

    public function getList(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getList($this->apiKey) : [];
    }

    public function getChart(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getChart($this->apiKey) : [];
    }

    public function getPhoneDetails(): array {
        $this->assertCanView();

        return $this->apiKey && $this->id ? $this->controller->getPhoneDetails($this->id, $this->apiKey) : [];
    }

    public function enrichEntity(): array {
        $this->assertCanEdit();

        if (!$this->apiKey) {
            return [];
        }

        $enrichmentKey  = sentrion('utils')->apiKeys->getCurrentOperatorEnrichmentKeyString();
        $entityId       = sentrion('utils')->conversion->getIntRequestParam('entityId', true);

        return $this->controller->enrichEntity($entityId, $enrichmentKey, $this->apiKey);
    }
}
