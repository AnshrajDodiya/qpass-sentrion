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

namespace Sentrion\Models\Query;

class Referers extends \Sentrion\Models\Query\Base {
    public function __construct(int $key) {
        $this->table = 'event_referer';
        $this->model = 'referers';

        $this->fields = [
            'event_referer.id'          => 'referer_id',
            'event_referer.referer'     => 'referer_referer',
            'event_referer.lastseen'    => 'referer_lastseen',
            'event_referer.created'     => 'referer_created',
        ];

        parent::__construct($key);
    }
}
