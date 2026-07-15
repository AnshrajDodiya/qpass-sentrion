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

class DictManager {
    public static function load(string $file): void {
        $locale = sentrion('storage')->get('LOCALES');
        $language = sentrion('storage')->get('LANGUAGE');

        $file = ucfirst($file);

        $path = sprintf('%s%s/Additional/%s.php', $locale, $language, $file);

        $isFileExists = file_exists($path);

        if ($isFileExists) {
            $values = include $path;

            if ($values !== false) {
                foreach ($values as $key => $value) {
                    sentrion('storage')->set($key, $value);
                }
            }
        }
    }
}
