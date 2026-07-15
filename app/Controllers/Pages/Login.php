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

class Login extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'login';
    protected bool $allowGuest = true;

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        $pageParams = [];

        $params = sentrion('utils')->render->extractRequestParams(['token', 'email', 'password']);
        $errorCode = sentrion('utils')->validators->validateLogin($params);

        $pageParams['VALUES'] = $params;
        $pageParams['ERROR_CODE'] = $errorCode;

        if ($errorCode) {
            return $pageParams;
        }

        sentrion('utils')->updates->syncUpdates();

        $email      = sentrion('utils')->conversion->getStringRequestParam('email');
        $password   = sentrion('utils')->conversion->getStringRequestParam('password');

        $operatorId = sentrion('models')->operator->getActivatedByEmail($email);

        if ($operatorId && $operatorId > sentrion('utils')->constants->RESERVED_OPERATOR_IDS && sentrion('models')->operator->verifyPassword($password, $operatorId)) {
            $this->proceedSuccessfulLogin($operatorId);
            sentrion('response')->redirect('/');
        } else {
            $pageParams['VALUES'] = sentrion('utils')->routes->callExtra('LOGIN_FAIL', $params) ?? $params;
            $pageParams['ERROR_CODE'] = sentrion('utils')->errorCodes->EMAIL_OR_PASSWORD_IS_NOT_CORRECT;
        }

        return $pageParams;
    }

    protected function proceedSuccessfulLogin(int $operatorId): void {
        sentrion('session')->clear();
        session_commit();

        sentrion('session')->set('active_user_id', $operatorId);
        sentrion('session')->set('active_key_id', sentrion('utils')->apiKeys->getFirstKeyByOperatorId($operatorId));

        sentrion('utils')->routes->setCurrentRequestOperator();
        sentrion('utils')->routes->setCurrentRequestApiKey();

        $this->apiKey = sentrion('utils')->access->getCurrentOperatorApiKeyId();

        // blacklist first because it uses review_queue_updated_at for cache check
        sentrion('controllers')->blacklist->setBlacklistUsersCount(true, $this->apiKey);        // use cache
        sentrion('controllers')->reviewQueue->setNotReviewedCount(true, $this->apiKey);         // use cache
    }

    protected function isAllowed(): bool {
        if (!sentrion('utils')->variables->completedConfig()) {
            sentrion('response')->error(422);
        }

        return parent::isAllowed();
    }

    public function getPageParams(): array {
        $this->assertCanView();

        $pageParams = [
            'HTML_FILE'             => 'login.html',
            'JS'                    => 'user_main.js',
            'ALLOW_FORGOT_PASSWORD' => sentrion('utils')->variables->getForgotPasswordAllowed(),
            'INTERNAL_PAGE'         => false,
        ];

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        return array_merge($pageParams, $postParams);
    }
}
