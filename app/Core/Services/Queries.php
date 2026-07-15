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

class Queries extends BaseAggregator {
    protected string $namespace = '\\Sentrion\\Models\\Query\\%s';
    protected bool $singletons = false;
    // query builders (Sentrion\Models\Query\*), each scoped to the current API key
    protected array $objectsMap = [
        'countries'         => 'Countries',
        'devices'           => 'Devices',
        'events'            => 'Events',
        'ips'               => 'Ips',
        'payloads'          => 'Payloads',
        'queries'           => 'Queries',
        'referers'          => 'Referers',
        'sessions'          => 'Sessions',
        'urls'              => 'Urls',
        'users'             => 'Users',
    ];

    // Query builders require the API key in their constructor, so instantiate
    // them scoped to the current session's key.
    protected function createObject(string $name, string $className, bool $getFullClass): object {
        $currentKey = sentrion('session')->getCurrentKey();
        $key = $currentKey->id ?? 0;
        $class = $this->getClassName($className, $getFullClass);

        return new $class($key);
    }
}
