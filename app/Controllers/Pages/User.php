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

class User extends \Sentrion\Controllers\Pages\Base {
    public string $page = 'user';

    protected function proceedPostRequest(int $apiKey): array {
        $this->assertCanEdit();

        $cmd = sentrion('utils')->conversion->getStringRequestParam('cmd');

        if ($cmd === 'delete') {
            $this->assertCanDelete();
        }

        return match ($cmd) {
            'riskScore'     => sentrion('controllers')->user->recalculateRiskScore($apiKey),
            'delete'        => sentrion('controllers')->user->deleteUser($apiKey),
            'reenrichment'  => sentrion('controllers')->enrichment->enrichEntityFromRequest($apiKey),
            default => []
        };
    }

    protected function getPageParams(): array {
        $this->assertCanView();

        $userId = sentrion('utils')->conversion->getIntUrlParam('userId');
        $hasAccess = sentrion('controllers')->user->checkIfOperatorHasAccess($userId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $postParams = sentrion('request')->isPost() ? $this->proceedPostRequest($this->apiKey) : [];

        [$scheduledForDeletion, $errorCode] = sentrion('controllers')->user->getScheduledForDeletion($userId, $this->apiKey);
        $user = sentrion('controllers')->user->getUserById($userId, $this->apiKey);

        $pageTitle      = sentrion('utils')->render->getInternalPageTitleWithPostfix($user['page_title']);
        $enrichmentOn   = sentrion('controllers')->user->checkEnrichmentAvailability();

        $pageParams = [
            'LOAD_DATATABLE'                => true,
            'LOAD_JVECTORMAP'               => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'HTML_FILE'                     => 'user.html',
            'LOAD_UPLOT'                    => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'USER'                          => $user,
            'SCHEDULED_FOR_DELETION'        => $scheduledForDeletion,
            'PAGE_TITLE'                    => $pageTitle,
            'ENRICHMENT'                    => $enrichmentOn,
            'JS'                            => 'user.js',
            'ERROR_CODE'                    => $errorCode,
            'SEARCH_PLACEHOLDER'            => sentrion('storage')->get('fieldAudits_search_placeholder'),
            'INTERNAL_PAGE'                 => true,
        ];

        [$scheduledForBlacklist, $errorCode] = sentrion('controllers')->user->getScheduledForBlacklist($userId, $this->apiKey);
        if ($scheduledForBlacklist) {
            sentrion('session')->set('extra_message_code', $errorCode ?? sentrion('utils')->errorCodes->USER_BLACKLISTING_QUEUED);
        }

        return array_merge($pageParams, $postParams);
    }

    public function manageUser(): array {
        $this->assertCanEdit();

        $timer = sentrion('request')->setTimer();
        $accountId  = sentrion('utils')->conversion->getIntRequestParam('userId');
        $cmd        = sentrion('utils')->conversion->getStringRequestParam('type');
        $hasAccess  = $this->controller->checkIfOperatorHasAccess($accountId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        $successCode = false;

        switch ($cmd) {
            case 'add':
                $this->controller->addToWatchlist($accountId, $this->apiKey);
                $successCode = sentrion('utils')->errorCodes->USER_ADDED_TO_WATCHLIST;
                break;

            case 'remove':
                $this->controller->removeFromWatchlist($accountId, $this->apiKey);
                $successCode = sentrion('utils')->errorCodes->USER_REMOVED_FROM_WATCHLIST;
                break;

            case 'fraud':
                $this->controller->addToBlacklistQueue($accountId, true, false, true, $this->apiKey);   // recalculate
                $successCode = sentrion('utils')->errorCodes->USER_FRAUD_FLAG_SET;
                break;

            case 'legit':
                $this->controller->addToBlacklistQueue($accountId, false, false, true, $this->apiKey);  // recalculate
                $successCode = sentrion('utils')->errorCodes->USER_FRAUD_FLAG_UNSET;
                break;

            case 'add-to-review':
                $this->controller->addToReviewQueue($accountId, $this->apiKey);     // set added_to_review = NOW() & fraud = null
                $successCode = sentrion('utils')->errorCodes->USER_ADDED_TO_REVIEW;
                break;
        }

        sentrion('log')->debug('complete manageUser() with command %s in %f.', $cmd, sentrion('request')->getTimer($timer));

        return  ['success' => $successCode];
    }

    public function getSparklinesChart(): array {
        $this->assertCanView();

        return $this->apiKey ? $this->controller->getSparklinesChart($this->apiKey) : [];
    }

    public function getUserScoreDetails(): array {
        $this->assertCanView();

        $userId = sentrion('utils')->conversion->getIntRequestParam('userId');

        return $this->controller->getUserScoreDetails($userId, $this->apiKey);
    }

    public function getUserDetails(): array {
        $this->assertCanView();

        $userId = sentrion('utils')->conversion->getIntRequestParam('userId');
        $hasAccess = $this->controller->checkIfOperatorHasAccess($userId, $this->apiKey);

        if (!$hasAccess) {
            sentrion('response')->error(404);
        }

        return $this->controller->getUserDetails($userId, $this->apiKey);
    }
}
