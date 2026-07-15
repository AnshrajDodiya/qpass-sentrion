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

class Isp extends \Sentrion\Controllers\Services\Base {
    public function checkIfOperatorHasAccess(int $ispId, int $apiKey): bool {
        return sentrion('models')->isp->checkAccess($ispId, $apiKey);
    }

    public function getFullIspInfoById(int $ispId, int $apiKey): array {
        $result = sentrion('models')->isp->getFullIspInfoById($ispId, $apiKey);
        $result['lastseen'] = sentrion('utils')->elapsedDate->short($result['lastseen']);

        return $result;
    }

    private function getNumberOfIpsByIspId(int $ispId, int $apiKey): int {
        return sentrion('models')->isp->getIpCountById($ispId, $apiKey);
    }

    public function getIspDetails(int $ispId, int $apiKey): array {
        $result = [];
        $data = $this->getFullIspInfoById($ispId, $apiKey);

        if (array_key_exists('asn', $data)) {
            $result = [
                'asn'           => $data['asn'],
                'total_fraud'   => $data['total_fraud'],
                'total_visit'   => $data['total_visit'],
                'total_account' => $data['total_account'],
                'total_ip'      => $this->getNumberOfIpsByIspId($ispId, $apiKey),
            ];
        }

        return $result;
    }
}
