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

namespace Sentrion\Crons;

class RetentionPolicyViolations extends Base {
    public function process(): void {
        $this->addLog('Start retention policy violations.');

        $retentionKeys = sentrion('models')->retentionPolicies->getRetentionKeys();
        $cnt = 0;
        $fieldCnt = 0;

        foreach ($retentionKeys as $key) {
            // insuring clause
            if ($key['retention_policy'] > 0) {
                $cnt += sentrion('models')->events->retentionDeletion($key['retention_policy'], $key['id']);
                $fieldCnt += sentrion('models')->fieldAuditTrail->retentionDeletion($key['retention_policy'], $key['id']);
            }
        }

        $this->addLog(sprintf('Deleted %s events and %s field audit trails for %s operators due to retention policy violations.', $cnt, $fieldCnt, count($retentionKeys)));
    }
}
