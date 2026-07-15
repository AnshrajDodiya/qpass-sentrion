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

namespace Sentrion\Core\Services;

class Storage {
    public function get(string $key): mixed {
        return sentrion('router')->get($key);
    }

    public function set(string $key, mixed $value): mixed {
        return sentrion('router')->set($key, $value);
    }

    public function remove(string $key): mixed {
        return sentrion('router')->clear($key);
    }
}
