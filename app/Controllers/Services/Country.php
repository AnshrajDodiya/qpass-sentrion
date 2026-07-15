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

namespace Sentrion\Controllers\Services;

class Country extends \Sentrion\Controllers\Services\Base {
    public function checkIfOperatorHasAccess(int $countryId, int $apiKey): bool {
        return sentrion('models')->country->checkAccess($countryId, $apiKey);
    }

    public function getCountryById(int $countryId, int $apiKey): array {
        return sentrion('models')->country->getCountryById($countryId, $apiKey);
    }
}
