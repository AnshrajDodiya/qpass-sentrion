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

// NOTE: has nested entities
class EmptyQuery extends \Sentrion\Entities\BaseEmpty {
    protected ?int $id = null;
    protected ?string $query = null;
    protected \Sentrion\Entities\Resource|\Sentrion\Entities\EmptyResource $resource;

    protected ?string $lastseen = null;
    protected ?string $created = null;

    protected ?int $key = null;

    protected array $nestedProps = ['resource'];
    protected array $tsFields = ['created', 'lastseen'];

    protected function setAdditional(): void {
        $this->resource = sentrion('entities')->emptyResource->get();
    }
}
