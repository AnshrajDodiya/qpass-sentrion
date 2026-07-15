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

class VersionControl {
    public const VERSION_MAJOR = 0;
    public const VERSION_MINOR = 10;
    public const VERSION_REVISION = 0;

    public static function versionString(): string {
        return sprintf('%d.%d.%d', self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_REVISION);
    }

    public static function fullVersionString(): string {
        return sprintf('v%d.%d.%d', self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_REVISION);
    }
}
