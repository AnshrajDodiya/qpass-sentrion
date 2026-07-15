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

class ForgotPassword extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'forgotPassword';
    protected bool $allowGuest = true;

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        $pageParams = [];

        $params = sentrion('utils')->render->extractRequestParams(['token', 'email']);
        $errorCode = sentrion('utils')->validators->validateForgotPassword($params);

        if (!$errorCode) {
            $email = sentrion('utils')->conversion->getStringRequestParam('email');
            $operatorId = sentrion('models')->operator->getActivatedByEmail($email);

            if ($operatorId) {
                // Create forgot password record.
                $renewKey = sentrion('models')->forgotPassword->insertRecord($operatorId);
                // Send forgot password email.
                $this->sendPasswordRenewEmail($operatorId, $renewKey);
            }

            // Random sleep between 0.5 and 1 second to prevent timing attacks.
            usleep(rand(500000, 1000000));

            // Always report back that the email was sent.
            $pageParams['SUCCESS_CODE'] = sentrion('utils')->errorCodes->RENEW_KEY_CREATED;
        }

        $pageParams['VALUES'] = $params;
        $pageParams['ERROR_CODE'] = $errorCode;

        return $pageParams;
    }

    protected function isAllowed(): bool {
        return parent::isAllowed() && sentrion('utils')->variables->getForgotPasswordAllowed();
    }

    public function getPageParams(): array {
        $this->assertCanView();

        $pageParams = [
            'HTML_FILE'     => 'forgotPassword.html',
            'INTERNAL_PAGE' => false,
        ];

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        return array_merge($pageParams, $postParams);
    }

    private function sendPasswordRenewEmail(int $operatorId, string $renewKey): void {
        $url = sentrion('utils')->variables->getHostWithProtocolAndBase();

        $operator = sentrion('entities')->operator->getById($operatorId);

        $toName = $operator->firstname;
        $toAddress = $operator->email;

        $subject = sentrion('storage')->get('ForgotPassowrd_renew_password_subject');
        $message = sentrion('storage')->get('ForgotPassowrd_renew_password_body');

        $renewUrl = sprintf('%s/password-recovering/%s', $url, $renewKey);
        $message = sprintf($message, $renewUrl);

        sentrion('utils')->mailer->send($toName, $toAddress, $subject, $message);
    }
}
