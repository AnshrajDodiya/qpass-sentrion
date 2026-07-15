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

namespace Sentrion\Models;

class ReviewQueue extends \Sentrion\Models\Base {
    public function getCount(int $apiKey): int {
        $params = [
            ':api_key'  => $apiKey,
        ];

        $query = (
            'SELECT
                COUNT(*) AS count

            FROM
                event_account

            WHERE
                event_account.key = :api_key AND
                event_account.fraud IS NULL AND
                event_account.added_to_review IS NOT NULL'
        );

        return $this->execQuery($query, $params)[0]['count'] ?? 0;
    }
}
