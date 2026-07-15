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

class Rules extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'rules';

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        return match (sentrion('utils')->conversion->getStringRequestParam('cmd')) {
            'changeThresholdValues' => sentrion('controllers')->rules->changeThresholdValues(),
            'refreshRules'          => sentrion('controllers')->rules->refreshRules(),
            'applyRulesPreset'      => sentrion('controllers')->rules->applyRulesPreset(),
            default => []
        };
    }

    public function getPageParams(): array {
        $this->assertCanView();

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        [$isOwner, $apiKeys] = sentrion('utils')->apiKeys->getOperatorApiKeys($this->operator->id);

        $pageParams = [
            'LOAD_DATATABLE'        => true,
            'LOAD_AUTOCOMPLETE'     => true,
            'HTML_FILE'             => 'rules.html',
            'JS'                    => 'rules.js',
            'RULES_PRESETS'         => sentrion('assets')->rulesPresets->getPresets(),
            'BASE_PRESET_ID'        => sentrion('utils')->constants->BASE_RULE_PRESET_ID,
            'IS_OWNER'              => $isOwner,
            'API_KEYS'              => $apiKeys,
            'INTERNAL_PAGE'         => true,
        ];

        return array_merge($pageParams, $postParams);
    }

    public function getList(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getList($this->apiKey) : [];
    }

    public function saveRule(): array {
        $this->assertCanEdit();

        $ruleUid    = sentrion('utils')->conversion->getStringRequestParam('rule');
        $score      = sentrion('utils')->conversion->getIntRequestParam('value');

        $this->controller->saveUserRule($ruleUid, $score, $this->apiKey);

        return ['success' => true];
    }

    public function checkRule(): array {
        $this->assertCanView();

        set_time_limit(0);
        ini_set('max_execution_time', '0');

        $ruleUid    = sentrion('utils')->conversion->getStringRequestParam('ruleUid');

        [$allUsersCnt, $users] = $this->controller->checkRule($ruleUid, $this->apiKey);
        $proportion = $this->controller->getRuleProportion($allUsersCnt, count($users));

        return [
            'users'                 => array_slice($users, 0, sentrion('utils')->constants->RULE_CHECK_USERS_PASSED_TO_CLIENT),
            'count'                 => count($users),
            'section'               => $allUsersCnt,
            'proportion'            => $proportion,
            'proportion_updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
