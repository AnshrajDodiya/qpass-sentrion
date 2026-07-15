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

class NotificationsHandler extends Base {
    public function process(): void {
        $operators = sentrion('models')->notification->operatorsToNotify();

        $cnt = 0;
        $failed = 0;

        foreach ($operators as $operator) {
            if (sentrion('utils')->cron->checkTimezone($operator['timezone'] ?? '')) {
                try {
                    $name   = $operator['firstname'] ?? '';
                    $email  = $operator['email'] ?? '';
                    $review = $operator['review_queue_cnt'] ?? 0;
                    if (!sentrion('utils')->cron->sendUnreviewedItemsReminderEmail($name, $email, $review)) {
                        $this->addLog(sprintf('Username `%s` is not email; review count is %s', $email, $review));
                    }
                    sentrion('models')->notification->updateUnreviewedReminder($operator['id']);
                    $cnt++;
                } catch (\Throwable $e) {
                    $this->addLog(sprintf('Notification handler error %s.', $e->getMessage()));
                    $failed++;
                }
            }
        }

        $this->addLog(sprintf('Sent %s unreviewed items reminder notifications, failed %s.', $cnt, $failed));
    }
}
