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

namespace Sentrion\Updates;

abstract class Base {
    public static string $version = '';

    abstract public static function apply(\DB\SQL $database): void;

    public static function isApplied(\Sentrion\Models\Updates $updatesModel): bool {
        return $updatesModel->isApplied(static::$version, 'core');
    }

    protected static function regularSequence(string $name): string {
        return 'CREATE SEQUENCE ' . $name . (' AS BIGINT
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1');
    }
}
