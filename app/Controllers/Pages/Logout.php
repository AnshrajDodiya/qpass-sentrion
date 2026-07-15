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

namespace Sentrion\Controllers\Pages;

class Logout extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'logout';

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        $pageParams = [];

        $params = sentrion('utils')->render->extractRequestParams(['token']);
        $errorCode = sentrion('utils')->access->CSRFTokenValid($params);

        if (!$errorCode) {
            sentrion('session')->clear();
            session_commit();
            sentrion('response')->redirect('/');
        }

        $pageParams['ERROR_CODE'] = $errorCode;

        return $pageParams;
    }

    public function getPageParams(): array {
        $this->assertCanView();

        $pageParams = [
            'HTML_FILE'     => 'logout.html',
            'JS'            => 'user_main.js',
            'INTERNAL_PAGE' => false,
        ];

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        return array_merge($pageParams, $postParams);
    }

    protected function getRequiredPermission(): int {
        return sentrion('utils')->constants->PAGE_VIEW_PERMISSION_ID;
    }
}
