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

abstract class BaseQueue extends Base {
    abstract protected function processItem(array $item): void;

    protected function readyToProcess(string $action): bool {
        $result = sentrion('models')->queue->checkExecuting($action);

        if (!$result) {
            return true;    // no executing action
        }

        if (!sentrion('utils')->dateRange->isQueueTimeouted($result['updated'])) {
            return false;   // previous job still executing
        }

        $culprit = $result['event_account'];
        $allStuckIds = sentrion('models')->queue->getAllExecuting($action);

        // set failed for all stuck accounts
        sentrion('models')->queue->setFailedForStuckAction($action);

        $culpritIdx = array_search($culprit, $allStuckIds);
        if ($culpritIdx !== false) {
            unset($allStuckIds[$culpritIdx]);
            $allStuckIds = array_values($allStuckIds);
        }

        // add back to queue all accounts excluding culprit
        sentrion('models')->queue->addBatchIds($allStuckIds, $action);

        $this->addLog(sprintf('Uncloging stuck queue (now - updated > 30 minutes) on account %d. Added %d accounts back to queue.', $result['event_account'], count($allStuckIds)));

        return true; // set failed on stuck, can continue
    }

    protected function baseProcess(string $action): void {
        $prefix = '';

        switch ($action) {
            case sentrion('utils')->constants->DELETE_USER_QUEUE_ACTION_TYPE:
                $prefix = 'Deletion';
                break;
            case sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE:
                $prefix = 'Blacklist';
                break;
            case sentrion('utils')->constants->ENRICHMENT_QUEUE_ACTION_TYPE:
                $prefix = 'Enrichment';
                break;
            case sentrion('utils')->constants->RISK_SCORE_QUEUE_ACTION_TYPE:
                $prefix = 'Risk score';
                break;
        }

        if (!$prefix || !$this->readyToProcess($action)) {
            $this->addLog($prefix . ' queue is already being executed by another cron job.');

            return;
        }

        $this->addLog('Start processing queue.');

        $start = time();
        $success = [];
        $failed = [];
        $errors = [];
        $batch = [];
        $bottom = false;

        while (!$bottom) {
            $batchSize = sentrion('utils')->variables->getAccountOperationQueueBatchSize();

            // status waiting action deletion, older first
            $batch = sentrion('models')->queue->getNextBatchInQueue($action, $batchSize);

            $this->addLog(sprintf('Fetching next batch (%s/%s) in queue.', count($batch), $batchSize));

            if (!$batch) {
                break;
            }

            sentrion('models')->queue->setExecuting(array_column($batch, 'id'));

            foreach ($batch as $item) {
                if (!$item) {
                    break;
                }
                try {
                    $this->processItem($item);
                    $success[] = $item['id'];
                } catch (\Throwable $e) {
                    $failed[] = $item['id'];
                    $this->addLog(sprintf('Queue error %s.', $e->getMessage()));
                    if (!$errors) {
                        $errors[] = sprintf('Error on %s: %s. Trace: %s', json_encode($item), $e->getMessage(), $e->getTraceAsString());
                    }
                }

                // exit if took too long
                $batchTimeout = (time() - $start) > sentrion('utils')->constants->ACCOUNT_OPERATION_QUEUE_EXECUTE_TIME_SEC;
                if ($batchTimeout) {
                    break;
                }
            }
            // exit if took too long
            $bottom = (time() - $start) > sentrion('utils')->constants->ACCOUNT_OPERATION_QUEUE_EXECUTE_TIME_SEC;
        }

        sentrion('models')->queue->setCompleted($success);
        sentrion('models')->queue->setFailed($failed);
        sentrion('models')->queue->setWaiting(array_diff(array_diff(array_column($batch, 'id'), $success), $failed));     // not fitted

        if (count($errors)) {
            $errObj = [
                'code'      => 500,
                'message'   => sprintf('Cron %s err', get_class($this)),
                'trace'     => $errors[0],
                'sql_log'   => '',
            ];
            sentrion('utils')->errorHandler->saveErrorInformation($errObj);
        }

        $this->addLog(sprintf(
            'Processed %s items in %s seconds. %s items failed. %s items put back in queue.',
            count($success),
            time() - $start,
            count($failed),
            count($batch),
        ));
    }
}
