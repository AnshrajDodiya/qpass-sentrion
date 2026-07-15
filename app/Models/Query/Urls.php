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

class Urls extends \Sentrion\Models\Query\Base {
    public function __construct(int $key) {
        $this->table = 'event_url';
        $this->model = 'resources';

        $this->fields = [
            'event_url.id'              => 'url_id',
            'event_url.url'             => 'url_url',
            'event_url.title'           => 'url_title',
            'event_url.http_code'       => 'url_http_code',

            'event_url.total_visit'     => 'url_total_visit',
            'event_url.total_ip'        => 'url_total_ip',
            'event_url.total_device'    => 'url_total_device',
            'event_url.total_account'   => 'url_total_account',
            'event_url.total_country'   => 'url_total_country',
            'event_url.total_edit'      => 'url_total_edit',

            'event_url.lastseen'        => 'url_lastseen',
            'event_url.created'         => 'url_created',
            'event_url.updated'         => 'url_updated',
        ];

        parent::__construct($key);
    }
}
