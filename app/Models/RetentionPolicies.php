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

class RetentionPolicies extends \Sentrion\Models\Base {
    public function getRetentionKeys(): array {
        $query = (
            'SELECT
                dshb_api.id,
                dshb_api.retention_policy
            FROM
                dshb_api
            WHERE
                dshb_api.retention_policy > 0'
        );

        return $this->execQuery($query, null);
    }
}
