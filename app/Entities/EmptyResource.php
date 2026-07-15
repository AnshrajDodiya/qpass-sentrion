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

namespace Sentrion\Entities;

class EmptyResource extends \Sentrion\Entities\BaseEmpty {
    protected ?int $id = null;
    protected ?string $url = null;
    protected ?string $title = null;
    protected ?int $httpCode = null;

    protected ?int $totalVisit = null;
    protected ?int $totalIp = null;
    protected ?int $totalDevice = null;
    protected ?int $totalAccount = null;
    protected ?int $totalCountry = null;
    protected ?int $totalEdit = null;

    protected ?string $lastseen = null;
    protected ?string $created = null;
    protected ?string $updated = null;

    protected ?int $key = null;

    protected array $nestedProps = [];
    protected array $tsFields = ['created', 'lastseen', 'updated'];
}
