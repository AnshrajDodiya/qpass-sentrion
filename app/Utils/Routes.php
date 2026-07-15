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

namespace Sentrion\Utils;

class Routes {
    public static function getCurrentRequestOperator(): \Sentrion\Entities\Operator {
        return sentrion('storage')->get('CURRENT_USER') ?? sentrion('entities')->operator->getById(sentrion('utils')->constants->GUEST_OPERATOR_ID);
    }

    public static function setCurrentRequestOperator(): void {
        sentrion('storage')->set('CURRENT_USER', self::getCurrentSessionOperator());
    }

    public static function getCurrentSessionOperator(): \Sentrion\Entities\Operator {
        $loggedInOperatorId = sentrion('utils')->conversion->intValCheckEmpty(sentrion('session')->get('active_user_id'));
        $loggedInOperatorId = $loggedInOperatorId ? $loggedInOperatorId : sentrion('utils')->constants->GUEST_OPERATOR_ID;

        if (!sentrion('models')->operatorsRoles->tableExists()) {
            sentrion('utils')->updates->syncUpdates();
        }

        return sentrion('entities')->operator->getById($loggedInOperatorId);
    }

    public static function getCurrentRequestApiKey(): ?\Sentrion\Entities\ApiKey {
        return sentrion('storage')->get('CURRENT_KEY');
    }

    public static function setCurrentRequestApiKey(): void {
        sentrion('storage')->set('CURRENT_KEY', self::getCurrentSessionApiKey());
    }

    public static function getCurrentSessionApiKey(): ?\Sentrion\Entities\ApiKey {
        $keyId = sentrion('storage')->get('TEST_API_KEY_ID');

        if (!$keyId) {
            $keyId = sentrion('utils')->conversion->intValCheckEmpty(sentrion('session')->get('active_key_id'));
        }

        return $keyId ? sentrion('entities')->apiKey->getById($keyId) : null;
    }

    public static function callExtra(string $method, mixed ...$extra): string|array|null {
        $method = sentrion('storage')->get('EXTRA_' . $method);

        return $method && is_callable($method) ? $method(...$extra) : null;
    }
}
