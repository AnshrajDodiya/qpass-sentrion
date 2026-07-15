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

namespace Sensor\Model\Validated;

class HttpCode extends Base {
    private const INVALIDPLACEHOLDER = '0';
    public int $value;

    public function __construct(string $value) {
        parent::__construct($value, 'httpCode');
        $this->value = intval(ctype_digit($value) ? $value : self::INVALIDPLACEHOLDER);
        $this->invalid = !ctype_digit($value);
    }
}
