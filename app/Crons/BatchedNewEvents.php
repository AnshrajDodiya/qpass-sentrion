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

class BatchedNewEvents extends Base {
    protected function readyToProcess(): bool {
        // was not locked; locking now
        if (sentrion('models')->cursor->safeLock()) {
            return true;
        }

        $result = sentrion('models')->cursor->getLock();

        if (sentrion('utils')->dateRange->isQueueTimeouted($result['updated'])) {
            return false;
        }

        sentrion('models')->cursor->forceLock();

        return true; // relocked
    }

    public function process(): void {
        if (!$this->readyToProcess()) {
            $this->addLog('Could not acquire the lock; another cron is probably already working on recently added events.');

            return;
        }

        try {
            $cursor = sentrion('models')->cursor->getCursor();
            $next = sentrion('models')->cursor->getNextCursor($cursor, sentrion('utils')->variables->getNewEventsBatchSize());

            if (!$next) {
                $this->addLog('No new events.');
                sentrion('models')->cursor->unlock();

                return;
            }

            $accounts = sentrion('models')->events->getDistinctAccountsVisitLimit($cursor, $next);

            sentrion('utils')->routes->callExtra('BATCHING_NEW_EVENTS', $cursor, $next);

            sentrion('models')->queue->addBatch($accounts, sentrion('utils')->constants->RISK_SCORE_QUEUE_ACTION_TYPE);

            sentrion('models')->cursor->updateCursor($next);

            // TODO: Log new events cursor to database?
            $this->addLog('Updated \'last_event_id\' in \'queue_new_events_cursor\' table to ' . strval($next));
            $this->addLog(sprintf('Added %s accounts to the risk score queue.', count($accounts)));
        } catch (\Throwable $e) {
            $this->addLog(sprintf('Batched new events error %s.', $e->getMessage()));
        }

        sentrion('models')->cursor->unlock();
    }
}
