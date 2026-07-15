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

class Watchlist extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'watchlist';

    protected function getPageParams(): array {
        $this->assertCanView();

        $users = sentrion('controllers')->watchlist->getImportantUsers($this->apiKey);
        $searchPlacholder = sentrion('storage')->get('users_search_placeholder');

        return [
            'SEARCH_PLACEHOLDER'            => $searchPlacholder,
            'IMPORTANT_USERS'               => $users,
            'LOAD_DATATABLE'                => true,
            'LOAD_UPLOT'                    => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'HTML_FILE'                     => 'watchlist.html',
            'JS'                            => 'watchlist.js',
            'INTERNAL_PAGE'                 => true,
        ];
    }

    public function removeUserFromList(): array {
        $this->assertCanDelete();

        $userId = sentrion('utils')->conversion->getIntRequestParam('userId');

        $this->controller->removeFromWatchlist($userId, $this->apiKey);
        $successCode = sentrion('utils')->errorCodes->USER_REMOVED_FROM_WATCHLIST;

        return [
            'success' => $successCode,
            'userId' => $userId,
        ];
    }
}
