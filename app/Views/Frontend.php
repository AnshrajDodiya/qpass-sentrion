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

namespace Sentrion\Views;

class Frontend extends Base {
    public function render(): string|false|null {
        if ($this->data) {
            sentrion('router')->mset($this->data);
        }

        sentrion('utils')->routes->callExtra('FRONTEND_VIEW');

        // Use anti-CSRF token in templates.
        sentrion('storage')->set('CSRF', sentrion('session')->get('csrf'));

        $tpl = sentrion('storage')->get('TPL') ?? null;
        if ($tpl) {
            $tpl::registerExtends();
        }

        return \Template::instance()->render('templates/layout.html');
    }
}
