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

namespace Sentrion\Models\Grid\Resources;

class Grid extends \Sentrion\Models\Grid\Base\Grid {
    public function getResourcesByUserId(int $userId, int $apiKey): array {
        $params = [':account_id' => $userId];

        $data = $this->getGrid($apiKey, $this->idsModel->getResourcesIdsByUserId(), $params);
        if (isset($data['data'])) {
            $data['data'] = $this->extendWithSuspiciousUrl($data['data']);
        }

        return $data;
    }

    public function getAll(int $apiKey): array {
        $data = $this->getGrid($apiKey);
        if (isset($data['data'])) {
            $data['data'] = $this->extendWithSuspiciousUrl($data['data']);
        }

        return $data;
    }

    private function extendWithSuspiciousUrl(array $result): array {
        if (count($result)) {
            $suspiciousUrlList = sentrion('assets')->urlList->getList();
            foreach ($result as &$record) {
                $record['suspicious'] = $this->isUrlSuspicious($suspiciousUrlList, $record['url']);
            }
            unset($record);
        }

        return $result;
    }

    private function isUrlSuspicious(array $substrings, string $url): bool {
        foreach ($substrings as $sub) {
            if (stripos($url, $sub) !== false) {
                return true;
            }
        }

        return false;
    }
}
