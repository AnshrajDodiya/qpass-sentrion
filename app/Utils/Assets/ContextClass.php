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

class ContextClass extends Base {
    protected static function getDirectory(): string {
        return dirname(__DIR__, 3) . '/assets/rules/custom';
    }

    protected static function getClassFilename(string $filename): string {
        return self::getDirectory() . '/' . $filename;
    }

    protected static function getNamespace(): string {
        return '\\Sentrion\\Rules\\Custom';
    }

    public static function getContextObj(): ?\Sentrion\Assets\Context {
        $obj = null;

        $filename   = self::getClassFilename('Context.php');
        $cls        = self::getNamespace() . '\\Context';

        try {
            self::validateClass($filename, $cls);
            $obj = new $cls();
        } catch (\Throwable $e) {
            sentrion('log')->info('additional context file %s not found: %s.', $filename, $e->getMessage());
        }

        return $obj;
    }
}
