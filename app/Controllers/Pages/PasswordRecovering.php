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

class PasswordRecovering extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'passwordRecovering';
    protected bool $allowGuest = true;

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        $pageParams = [];

        $params = sentrion('utils')->render->extractRequestParams(['token', 'new-password', 'password-confirmation']);
        $errorCode = sentrion('utils')->validators->validatePasswordRecoveringPost($params);

        $pageParams['SUCCESS_CODE'] = 0;
        $pageParams['ERROR_CODE'] = $errorCode;

        if (!$errorCode) {
            $operatorId = sentrion('models')->forgotPassword->useByRenewKey(sentrion('request')->getUrlParam('renewKey'));
            $password = sentrion('utils')->conversion->getStringRequestParam('new-password');

            if ($operatorId) {
                sentrion('models')->operator->updatePassword($password, $operatorId);
                sentrion('models')->operator->activateByOperatorId($operatorId);
                $pageParams['SUCCESS_CODE'] = sentrion('utils')->errorCodes->ACCOUNT_ACTIVATED;
            } else {
                $pageParams['ERROR_CODE'] = sentrion('utils')->errorCodes->RENEW_KEY_IS_NOT_CORRECT;
            }
        }

        return $pageParams;
    }

    public function getPageParams(): array {
        $this->assertCanView();

        $pageParams = [
            'HTML_FILE'     => 'passwordRecovering.html',
            'INTERNAL_PAGE' => false,
        ];

        $errorCode = sentrion('utils')->validators->validatePasswordRecovering(sentrion('request')->getUrlParams());
        $pageParams['SUCCESS_CODE'] = $errorCode;

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        return array_merge($pageParams, $postParams);
    }
}
