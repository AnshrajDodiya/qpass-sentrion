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

namespace Sentrion\Utils\Assets;

class ServerConstantsClass extends Base {
    protected static function getDirectory(): string {
        return dirname(__DIR__, 3) . '/assets/rules';
    }

    protected static function getClassFilename(string $filename): string {
        return self::getDirectory() . '/' . $filename;
    }

    protected static function getNamespace(): string {
        return '\\Sentrion\\Rules';
    }

    public static function getConstantsObj(): ?\Sentrion\Assets\Constants {
        $obj = null;

        $filename   = self::getClassFilename('Constants.php');
        $cls        = self::getNamespace() . '\\Constants';

        try {
            self::validateClass($filename, $cls);
            $obj = new $cls();
        } catch (\Throwable $e) {
            sentrion('log')->info('rules constants file %s not found: %s.', $filename, $e->getMessage());
        }

        return $obj;
    }
}
