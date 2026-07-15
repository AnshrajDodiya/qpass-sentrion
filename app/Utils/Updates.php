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

class Updates {
    protected const UPDATES_LIST = [
        \Sentrion\Updates\Update001::class,
        \Sentrion\Updates\Update002::class,
        \Sentrion\Updates\Update003::class,
        \Sentrion\Updates\Update004::class,
        \Sentrion\Updates\Update005::class,
        \Sentrion\Updates\Update006::class,
        \Sentrion\Updates\Update007::class,
        \Sentrion\Updates\Update008::class,
        \Sentrion\Updates\Update009::class,
    ];

    public static function syncUpdates(): void {
        $applied = sentrion('models')->updates->checkDb('core', self::UPDATES_LIST);

        if ($applied) {
            // update only core rules
            sentrion('controllers')->rules->updateRules(false);
        }

        sentrion('utils')->routes->callExtra('UPDATES');
    }
}
