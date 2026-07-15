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

class Signup extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'signup';
    protected bool $allowGuest = true;

    protected function isAllowed(): bool {
        if (count(sentrion('models')->operator->getAll())) {
            sentrion('response')->error(404);
        }

        return parent::isAllowed();
    }

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        $pageParams = [];

        sentrion('utils')->updates->syncUpdates();

        $params = sentrion('utils')->render->extractRequestParams(['token', 'email', 'password', 'timezone', 'rules-preset']);
        $errorCode = sentrion('utils')->validators->validateSignup($params);

        $pageParams['ERROR_CODE'] = $errorCode;

        if ($errorCode) {
            $pageParams['VALUES'] = $params;
        } else {
            $operatorId = $this->addUser($params);

            $apiKey = $this->addDefaultApiKey($operatorId);
            sentrion('controllers')->rules->applyRulesPresetById($params['rules-preset'], sentrion('utils')->constants->PRIMARY_RULES_SET_ID, $apiKey);
            //$this->sendActivationEmail($operatorId);
            $pageParams['SUCCESS_CODE'] = sentrion('utils')->errorCodes->ACCOUNT_CREATED;
        }

        return $pageParams;
    }

    public function getPageParams(): array {
        $this->assertCanView();

        $pageParams = [
            'HTML_FILE'         => 'signup.html',
            'TIMEZONES'         => sentrion('utils')->timezones->timezonesList(),
            'RULES_PRESETS'     => sentrion('assets')->rulesPresets->getPresets(),
            'BASE_PRESET_ID'    => sentrion('utils')->constants->BASE_RULE_PRESET_ID,
            'INTERNAL_PAGE'     => false,
        ];

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        return array_merge($pageParams, $postParams);
    }

    private function addDefaultApiKey(int $operatorId): int {
        $skipEnrichingAttr = json_encode(array_keys(sentrion('utils')->constants->ENRICHING_ATTRIBUTES));

        return sentrion('models')->apiKeys->insertRecord($skipEnrichingAttr, true, $operatorId);
    }

    protected function addUser(array $data): int {
        $operatorId = sentrion('models')->operator->insertRecord($data['password'], $data['email'], $data['timezone']);
        sentrion('utils')->operatorAccess->addOperatorRoleById(sentrion('utils')->constants->GUEST_ROLE_ID, $operatorId);
        sentrion('utils')->operatorAccess->addOperatorRoleById(sentrion('utils')->constants->OPERATOR_ROLE_ID, $operatorId);

        return $operatorId;
    }

    /*private function sendActivationEmail(int $operatorId): void {
        $operator = sentrion('entities')->operator->getById($operatorId);
        $url = sentrion('utils')->variables->getHostWithProtocolAndBase();

        $toName = $operator->firstname;
        $toAddress = $operator->email;
        $activationKey = $operator->activationKey;

        $subject = sentrion('storage')->get('Signup_activation_email_subject');
        $message = sentrion('storage')->get('Signup_activation_email_body');

        $activationUrl = sprintf('%s/account-activation/%s', $url, $activationKey);
        $message = sprintf($message, $activationUrl);

        \Sentrion\Utils\Mailer::send($toName, $toAddress, $subject, $message);
    }*/
}
