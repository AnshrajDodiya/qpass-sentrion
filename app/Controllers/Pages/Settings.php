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

class Settings extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'settings';

    protected function proceedPostRequest(): array {
        $this->assertCanEdit();

        $cmd = sentrion('utils')->conversion->getStringRequestParam('cmd');

        if ($cmd === 'closeAccount') {
            $this->assertCanDelete();
        }

        return match ($cmd) {
            'changeEmail'                   => sentrion('controllers')->settings->changeEmail(),
            'changeTimezone'                => sentrion('controllers')->settings->changeTimezone(),
            'changePassword'                => sentrion('controllers')->settings->changePassword(),
            'closeAccount'                  => sentrion('controllers')->settings->closeAccount(),
            'updateNotificationPreferences' => sentrion('controllers')->settings->updateNotificationPreferences(),
            'changeRetentionPolicy'         => sentrion('controllers')->settings->changeRetentionPolicy(),
            'inviteCoOwner'                 => sentrion('controllers')->settings->inviteCoOwner(),
            'removeCoOwner'                 => sentrion('controllers')->settings->removeCoOwner(),
            'checkUpdates'                  => sentrion('controllers')->settings->checkUpdates(),
            default => []
        };
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest() : [];

        $currentOperator = sentrion('utils')->routes->getCurrentRequestOperator();
        $operatorId = $currentOperator->id;
        [$isOwner, $apiKeys] = sentrion('utils')->apiKeys->getOperatorApiKeys($operatorId);

        $pageParams = [
            'LOAD_DATATABLE'    => true,
            'LOAD_AUTOCOMPLETE' => true,
            'HTML_FILE'         => 'settings.html',
            'JS'                => 'settings.js',
            'TIMEZONES'         => sentrion('utils')->timezones->timezonesList(),
            'CURRENT_VERSION'   => sentrion('utils')->versionControl->fullVersionString(),
            'SHARED_OPERATORS'  => sentrion('controllers')->settings->getSharedApiKeyOperators($operatorId),
            'IS_OWNER'          => $isOwner,
            'API_KEYS'          => $apiKeys,
            'PROFILE'           => $currentOperator,
            'INTERNAL_PAGE'     => true,
        ];

        return array_merge($pageParams, $postParams);
    }
}
