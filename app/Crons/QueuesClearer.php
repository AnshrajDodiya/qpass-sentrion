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

class QueuesClearer extends Base {
    public const DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    public function process(): void {
        $days = sentrion('utils')->constants->ACCOUNT_OPERATION_QUEUE_CLEAR_COMPLETED_AFTER_DAYS;
        $before = (new \DateTime(strval($days) . ' days ago'))->format(self::DATETIME_FORMAT);

        $queues = [
            sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE,
            sentrion('utils')->constants->DELETE_USER_QUEUE_ACTION_TYPE,
            sentrion('utils')->constants->RISK_SCORE_QUEUE_ACTION_TYPE,
        ];

        $cnt = 0;

        // delete completed records
        foreach ($queues as $queue) {
            $cnt += sentrion('models')->queue->clearQueue($queue, $before);
        }

        $this->addLog(sprintf('Cleared %s completed items.', $cnt));
    }
}
