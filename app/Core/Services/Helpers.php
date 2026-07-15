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

namespace Sentrion\Core\Services;

class Helpers {
    public function formatTitle(string $title): string {
        $title = $title ? $title : sentrion('utils')->constants->UNAUTHORIZED_USERID;
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $title = sprintf('%s %s', $safeTitle, sentrion('utils')->constants->PAGE_TITLE_POSTFIX);

        return $title;
    }
}
