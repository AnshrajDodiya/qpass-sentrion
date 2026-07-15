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

class BlacklistQueueHandler extends BaseQueue {
    public function process(): void {
        parent::baseProcess(sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE);
    }

    protected function processItem(array $item): void {
        $fraud = true;

        $items = sentrion('controllers')->user->setFraudFlag(
            $item['event_account'],
            $fraud,
            $item['key'],
        );

        $username = sentrion('models')->user->getUserById($item['event_account'], $item['key'])['userid'] ?? '';

        $msg = sentrion('utils')->systemMessages->syslogLine(10, 5, 'BlacklistQueue', 'blacklisted userid=' . $username);
        sentrion('router')->write(sentrion('storage')->get('LOGS') . 'blacklist.log', $msg . PHP_EOL, true);

        $key = sentrion('entities')->apiKey->getById($item['key']);

        if (!$key->skipBlacklistSync && $key->token) {
            $userEmail = sentrion('models')->user->getUserById($item['event_account'], $item['key'])['email'] ?? null;

            if ($userEmail !== null) {
                $hashes = sentrion('utils')->cron->getHashes($items, $userEmail);
                $errorMessage = sentrion('utils')->cron->sendBlacklistReportPostRequest($hashes, $key->token, $key->id);
                if (strlen($errorMessage) > 0) {
                    // TODO: log error into database?
                    $this->addLog('Enrichment API cURL ' . $errorMessage);
                    $this->addLog('Enrichment API cURL logged to database.');
                }
            }
        }
    }
}
