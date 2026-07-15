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

namespace Sentrion\Controllers\Services;

class Settings extends \Sentrion\Controllers\Services\Base {
    public function getSharedApiKeyOperators(int $operatorId): array {
        return sentrion('models')->apiKeyCoOwner->getSharedApiKeyOperators($operatorId);
    }

    public function changePassword(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'current-password', 'new-password', 'password-confirmation']);
        $errorCode = sentrion('utils')->validators->validateChangePassword($params);

        if ($errorCode) {
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $password = sentrion('utils')->conversion->getStringRequestParam('new-password');
            $operatorId = sentrion('utils')->routes->getCurrentRequestOperator()->id;

            sentrion('models')->operator->updatePassword($password, $operatorId);

            // update operator obj
            sentrion('utils')->routes->setCurrentRequestOperator();

            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('settings_changePassword_success_message');
        }

        return $pageParams;
    }

    public function changeEmail(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'email']);
        $errorCode = sentrion('utils')->validators->validateChangeEmail($params);

        if ($errorCode) {
            $pageParams['EMAIL_VALUES'] = $params;
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $operatorId = sentrion('utils')->routes->getCurrentRequestOperator()->id;
            $email = sentrion('utils')->conversion->getStringRequestParam('email');

            sentrion('models')->operator->updateEmail($email, $operatorId);

            // update operator obj
            sentrion('utils')->routes->setCurrentRequestOperator();

            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('settings_changeEmail_success_message');
        }

        return $pageParams;
    }

    public function changeTimezone(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'timezone']);
        $errorCode = sentrion('utils')->validators->validateChangeTimezone($params);

        if ($errorCode) {
            $pageParams['TIME_ZONE_VALUES'] = $params;
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $timezone = sentrion('utils')->conversion->getStringRequestParam('timezone');
            $operatorId = sentrion('utils')->routes->getCurrentRequestOperator()->id;

            sentrion('models')->operator->updateTimezone($timezone, $operatorId);

            // update operator in f3 hive for clock
            sentrion('utils')->routes->setCurrentRequestOperator();

            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('settings_timezone_changeTimezone_success_message');
        }

        return $pageParams;
    }

    public function closeAccount(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token']);
        $errorCode = sentrion('utils')->validators->validateCloseAccount($params);

        if ($errorCode) {
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $operatorId = sentrion('utils')->routes->getCurrentRequestOperator()->id;
            sentrion('models')->operator->closeAccount($operatorId);
            sentrion('models')->operator->removeData($operatorId);

            sentrion('session')->clear();
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            } else {
                session_commit();
            }

            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('settings_closeAccount_success_message');
        }

        return $pageParams;
    }

    public function checkUpdates(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token']);
        $errorCode = sentrion('utils')->validators->validateCheckUpdates($params);

        if ($errorCode) {
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $currentVersion = sentrion('utils')->versionControl->versionString();

            $apiKey = sentrion('utils')->routes->getCurrentRequestApiKey()->id;

            $response = sentrion('utils')->network->sendApiRequest(null, '/version', 'GET', null, $apiKey);
            $code = $response->code;
            $result = $response->body;

            $statusCode = $code ?? 0;
            $errorMessage = $response->error ?? '';

            sentrion('log')->debug('checkUpdates /version API request with status code %d and response %s', $statusCode, json_encode($result));

            if (strlen($errorMessage) > 0 || $statusCode !== 200 || !is_array($result)) {
                $pageParams['ERROR_CODE'] = sentrion('utils')->errorCodes->ENRICHMENT_API_IS_NOT_AVAILABLE;
            } else {
                if (version_compare($currentVersion, $result['version'], '<')) {
                    $pageParams['SUCCESS_MESSAGE'] = sprintf('An update is available. Released date: %s.', $result['release_date']);
                } else {
                    $pageParams['SUCCESS_MESSAGE'] = 'Current version is up to date.';
                }
            }
        }

        return $pageParams;
    }

    public function updateNotificationPreferences(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'review-reminder-frequency']);
        $errorCode = sentrion('utils')->validators->validateUpdateNotificationPreferences($params);

        if ($errorCode) {
            $pageParams['PROFILE_VALUES'] = $params;
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $reminder = sentrion('utils')->conversion->getStringRequestParam('review-reminder-frequency');
            $operatorId = sentrion('utils')->routes->getCurrentRequestOperator()->id;

            sentrion('models')->operator->updateNotificationPreferences($reminder, $operatorId);

            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('settings_notificationPreferences_success_message');
        }

        return $pageParams;
    }

    public function changeRetentionPolicy(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'keyId', 'retention-policy']);
        $errorCode = sentrion('utils')->validators->validateRetentionPolicy($params);

        if ($errorCode) {
            $pageParams['RETENTION_POLICY_VALUES'] = $params;
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $keyId = sentrion('utils')->conversion->getIntRequestParam('keyId');
            $retentionPolicy = sentrion('utils')->conversion->getIntRequestParam('retention-policy');

            sentrion('models')->apiKeys->updateRetentionPolicy($retentionPolicy, $keyId);
            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('settings_retentionPolicy_changeTimezone_success_message');
        }

        return $pageParams;
    }

    public function inviteCoOwner(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'email']);
        $errorCode = sentrion('utils')->validators->validateInvitingCoOwner($params);

        if ($errorCode) {
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $currentOperator = sentrion('utils')->routes->getCurrentRequestOperator();
            $currentOperatorId = $currentOperator->id;

            $apiKey = sentrion('utils')->routes->getCurrentRequestApiKey();

            $params['timezone'] = 'UTC';
            $invitedOperatorId = sentrion('models')->operator->insertRecord(null, $params['email'], 'UTC');

            $renewKey = sentrion('models')->forgotPassword->insertRecord($invitedOperatorId);

            $this->makeOperatorCoOwner($invitedOperatorId, $apiKey->id);
            $this->sendInvitationEmail($params['email'], $currentOperatorId, $renewKey);

            $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('api_add_co_owner_success_message');
        }

        return $pageParams;
    }

    public function removeCoOwner(): array {
        $pageParams = [];
        $params = sentrion('utils')->render->extractRequestParams(['token', 'operatorId']);
        $errorCode = sentrion('utils')->validators->validateRemovingCoOwner($params);

        if ($errorCode) {
            $pageParams['ERROR_CODE'] = $errorCode;
        } else {
            $operatorId = sentrion('utils')->conversion->getIntRequestParam('operatorId');

            $keyId = sentrion('models')->apiKeyCoOwner->getCoOwnershipKeyId($operatorId);
            $apiKey = sentrion('utils')->routes->getCurrentSessionApiKey();

            if ($apiKey->id === $keyId && sentrion('utils')->routes->getCurrentRequestOperator()->id === $apiKey->creator) {
                sentrion('models')->apiKeyCoOwner->deleteCoOwnership($operatorId);

                sentrion('models')->operator->deleteAccount($operatorId);

                $pageParams['SUCCESS_MESSAGE'] = sentrion('storage')->get('api_remove_co_owner_success_message');
            } else {
                $pageParams['ERROR_MESSAGE'] = sentrion('storage')->get('api_remove_co_owner_error_message');
            }
        }

        return $pageParams;
    }

    protected function makeOperatorCoOwner(int $operatorId, int $apiKey): void {
        sentrion('models')->apiKeyCoOwner->insertRecord($operatorId, $apiKey);
    }

    protected function sendInvitationEmail(string $email, int $inviterId, string $renewKey): void {
        $toAddress = $email;

        $inviter = sentrion('entities')->operator->getById($inviterId);

        $site = sentrion('utils')->variables->getHostWithProtocolAndBase();

        $inviterDisplayName = $inviter->email;
        if ($inviter->firstname && $inviter->lastname) {
            $inviterDisplayName = sprintf('%s %s (%s)', $inviter->firstname, $inviter->lastname, $inviterDisplayName);
        }

        $toName = null;
        //$toAddress = $operator->email;

        $subject = sentrion('storage')->get('api_invitation_email_subject');
        $message = sentrion('storage')->get('api_invitation_email_body');

        $renewUrl = sprintf('%s/password-recovering/%s', $site, $renewKey);
        $message = sprintf($message, $inviterDisplayName, $renewUrl);

        sentrion('utils')->mailer->send($toName, $toAddress, $subject, $message);
    }
}
