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

class Grids extends BaseAggregator {
    protected string $namespace = '\\Sentrion\\Models\\Grid\\%s\\Grid';
    // skip Base and Data
    protected array $objectsMap = [
        'blacklist'         => 'Blacklist',
        'countries'         => 'Countries',
        'devices'           => 'Devices',
        'domains'           => 'Domains',
        'emails'            => 'Emails',
        'events'            => 'Events',
        'fieldAuditTrail'   => 'FieldAuditTrail',
        'fieldAudits'       => 'FieldAudits',
        'ips'               => 'Ips',
        'isps'              => 'Isps',
        'logbook'           => 'Logbook',
        'phones'            => 'Phones',
        'resources'         => 'Resources',
        'reviewQueue'       => 'ReviewQueue',
        'rules'             => 'Rules',
        'users'             => 'Users',
        'userAgents'        => 'UserAgents',
    ];
}
