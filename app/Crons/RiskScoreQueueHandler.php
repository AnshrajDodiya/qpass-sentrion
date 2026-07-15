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

class RiskScoreQueueHandler extends BaseQueue {
    private object $rulesController;

    public function __construct() {
        $this->rulesController = sentrion('controllers')->rules;
        $this->rulesController->buildEvaluationModels();
    }

    public function process(): void {
        $batchSize = sentrion('utils')->variables->getAccountOperationQueueBatchSize();
        $keys = sentrion('models')->queue->getNextBatchKeys(sentrion('utils')->constants->RISK_SCORE_QUEUE_ACTION_TYPE, $batchSize);

        parent::baseProcess(sentrion('utils')->constants->RISK_SCORE_QUEUE_ACTION_TYPE);

        foreach ($keys as $key) {
            sentrion('controllers')->blacklist->setBlacklistUsersCount(false, $key);
            sentrion('controllers')->reviewQueue->setNotReviewedCount(false, $key);
        }
    }

    protected function processItem(array $item): void {
        $this->rulesController->evaluateUser($item['event_account'], $item['key'], true);
    }
}
