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

class User extends \Sentrion\Controllers\Services\Base {
    public function getSparklinesChart(int $apiKey): array {
        return sentrion('charts')->userStats->getData($apiKey);
    }

    public function getUserDetails(int $userId, int $apiKey): array {
        sentrion('models')->user->updateTotalsByAccountIds([$userId], $apiKey);

        $model          = new \Sentrion\Models\UserDetails\Id();
        $userDetails    = $model->getDetails($userId, $apiKey);

        $model          = new \Sentrion\Models\UserDetails\Ip();
        $ipDetails      = $model->getDetails($userId, $apiKey);

        $model          = new \Sentrion\Models\UserDetails\Total();
        $totalDetails   = $model->getDetails($userId, $apiKey);

        $model          = new \Sentrion\Models\UserDetails\Behaviour();
        $offset         = sentrion('utils')->timezones->getCurrentOperatorOffset();

        $dateRange      = sentrion('utils')->timezones->getTodayRange($offset);
        $todayDetails   = $model->getDayDetails($userId, $dateRange, $apiKey);

        $dateRange          = sentrion('utils')->timezones->getYesterdayRange($offset);
        $yesterdayDetails   = $model->getDayDetails($userId, $dateRange, $apiKey);

        return [
            'userDetails'       => $userDetails,
            'ipDetails'         => $ipDetails,
            'totalDetails'      => $totalDetails,
            'todayDetails'      => $todayDetails,
            'yesterdayDetails'  => $yesterdayDetails,
        ];
    }

    public function recalculateRiskScore(int $apiKey): array {
        $result = [];
        set_error_handler([\Sentrion\Utils\ErrorHandler::class, 'exceptionErrorHandler']);

        try {
            $userId = sentrion('utils')->conversion->getIntRequestParam('accountid');

            [$score, $rules] = $this->getUserScore($userId, $apiKey);
            $result = [
                'SUCCESS_MESSAGE' => sentrion('storage')->get('user_recalculate_risk_score_success_message'),
                'score' => $score,
                'rules' => $rules,
            ];
        } catch (\ErrorException $e) {
            $result = ['ERROR_CODE' => sentrion('utils')->errorCodes->RISK_SCORE_UPDATE_UNKNOWN_ERROR];
        }

        restore_error_handler();

        return $result;
    }

    public function deleteUser(int $apiKey): void {
        // TODO: check apiKey + account owning
        if ($apiKey) {
            $accountId = sentrion('utils')->conversion->getIntRequestParam('accountid');
            $code = sentrion('utils')->errorCodes->REST_API_USER_ALREADY_DELETING;

            if (!sentrion('models')->queue->isInQueue($accountId, sentrion('utils')->constants->DELETE_USER_QUEUE_ACTION_TYPE, $apiKey)) {
                $code = sentrion('utils')->errorCodes->REST_API_USER_ADDED_FOR_DELETION;
                sentrion('models')->queue->add($accountId, sentrion('utils')->constants->DELETE_USER_QUEUE_ACTION_TYPE, $apiKey);
            }

            sentrion('session')->set('extra_message_code', $code);
            sentrion('response')->redirect('/id');
        }
    }

    public function getUserScoreDetails(int $userId, int $apiKey): array {
        $user = sentrion('models')->user->getUserById($userId, $apiKey);

        return [
            'score_details'     => sentrion('models')->user->getApplicableRulesByAccountId($userId, $apiKey, true),
            'score_calculated'  => $user !== [] ? $user['score'] !== null : false,
            'extended_score'    => sentrion('models')->userScore->getScoreDetailsByUserId($userId, $apiKey, true),
        ];
    }

    public function getUserById(int $accountId, int $apiKey): array {
        $user = sentrion('models')->user->getUserById($accountId, $apiKey);
        $rules = sentrion('models')->rules->getAll();

        $details = [];
        if ($user['score_details']) {
            $scoreDetails = json_decode($user['score_details'], true);

            foreach ($scoreDetails as $detail) {
                $score = $detail['score'] ?? null;
                $ruleUid = $detail['uid'] ?? null;
                if ($score !== 0 && isset($rules[$ruleUid])) {
                    $item = $rules[$ruleUid];
                    $item['score'] = $score;
                    $details[] = $item;
                }
            }
        }

        usort($details, [\Sentrion\Utils\Sort::class, 'cmpScore']);

        $user['score_details'] = $details;

        $pageTitle = $user['userid'];
        if ($user['firstname'] !== null && $user['firstname'] !== '') {
            $pageTitle .= sprintf(' (%s)', $user['firstname']);
        }
        if ($user['lastname'] !== null && $user['lastname'] !== '') {
            $pageTitle .= sprintf(' (%s)', $user['lastname']);
        }
        $user['page_title'] = $pageTitle;

        $tsColumns = ['created', 'lastseen', 'score_updated_at', 'latest_decision', 'updated', 'added_to_review'];
        $user = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $user);

        return $user;
    }

    public function checkIfOperatorHasAccess(int $userId, int $apiKey): bool {
        return sentrion('models')->user->checkAccess($userId, $apiKey);
    }

    public function checkEnrichmentAvailability(): bool {
        return sentrion('utils')->apiKeys->getCurrentOperatorEnrichmentKeyString() !== null;
    }

    public function addToWatchlist(int $accountId, int $apiKey): void {
        sentrion('models')->watchlist->add($accountId, $apiKey);
    }

    public function removeFromWatchlist(int $accountId, int $apiKey): void {
        sentrion('models')->watchlist->remove($accountId, $apiKey);
    }

    public function addToReviewQueue(int $accountId, int $apiKey): void {
        sentrion('models')->user->addToReviewQueue($accountId, $apiKey);
        sentrion('controllers')->reviewQueue->setNotReviewedCount(false, $apiKey);
    }

    public function addToBlacklistQueue(int $accountId, bool $fraud, bool $cron, bool $cnt, int $apiKey): void {
        $inQueue = sentrion('models')->queue->isInQueue($accountId, sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE, $apiKey);

        if (!$fraud) {
            $this->setFraudFlag($accountId, false, $apiKey); // Directly remove blacklisted items

            if ($inQueue) {
                sentrion('models')->queue->removeFromQueue($accountId, sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE, $apiKey); // Cancel queued operation
            }
        }

        if (!$inQueue && $fraud) {
            sentrion('models')->queue->add($accountId, sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE, $apiKey);
        }

        sentrion('models')->user->updateFraudFlag([$accountId], $apiKey, $fraud);

        if ($cnt) {
            sentrion('controllers')->blacklist->setBlacklistUsersCount(false, $apiKey);      // do not use cache
            sentrion('controllers')->reviewQueue->setNotReviewedCount(false, $apiKey);       // do not use cache
        }

        if (!$cron) {
            $this->setReviewedFlag($accountId, true, $apiKey);
        }

        sentrion('utils')->routes->callExtra('UPDATE_USER_FRAUD_STATUS', $accountId, $fraud, $cron, $apiKey);
    }

    /**
     * @param array{accountId: int, key: int}[] $accounts
     */
    public function addBatchToCalculateRiskScoreQueue(array $accounts): void {
        sentrion('models')->queue->addBatch($accounts, sentrion('utils')->constants->RISK_SCORE_QUEUE_ACTION_TYPE);
    }

    public function setReviewedFlag(int $accountId, bool $reviewed, int $apiKey): void {
        sentrion('models')->user->updateReviewedFlag($accountId, $reviewed, $apiKey);
    }

    public function getUserScore(int $accountId, int $apiKey): array {
        $total = 0;
        $rules = [];

        sentrion('controllers')->rules->evaluateUser($accountId, $apiKey);

        $rules = sentrion('models')->user->getApplicableRulesByAccountId($accountId, $apiKey);

        $total = $rules[0]['total_score'] ?? 0;
        array_walk($rules, function (&$rule): void {
            unset($rule['total_score']);
        }, $rules);

        return [$total, $rules];
    }

    public function getScheduledForDeletion(int $userId, int $apiKey): array {
        [$scheduled, $status] = sentrion('models')->queue->isInQueueStatus($userId, sentrion('utils')->constants->DELETE_USER_QUEUE_ACTION_TYPE, $apiKey);

        return [$scheduled, ($status === sentrion('utils')->constants->FAILED_QUEUE_STATUS_TYPE) ? sentrion('utils')->errorCodes->USER_DELETION_FAILED : null];
    }

    public function getScheduledForBlacklist(int $userId, int $apiKey): array {
        [$scheduled, $status] = sentrion('models')->queue->isInQueueStatus($userId, sentrion('utils')->constants->BLACKLIST_QUEUE_ACTION_TYPE, $apiKey);

        return [$scheduled, ($status === sentrion('utils')->constants->FAILED_QUEUE_STATUS_TYPE) ? sentrion('utils')->errorCodes->USER_BLACKLISTING_FAILED : null];
    }

    public function setFraudFlag(int $accountId, bool $fraud, int $apiKey): array {
        $ips    = sentrion('models')->blacklistItems->getIpsRelatedToAccountWithinOperator($accountId, $apiKey);
        $emails = sentrion('models')->blacklistItems->getEmailsRelatedToAccountWithinOperator($accountId, $apiKey);
        $phones = sentrion('models')->blacklistItems->getPhonesRelatedToAccountWithinOperator($accountId, $apiKey);

        $relatedIpsIds = array_column($ips, 'id');
        $relatedEmailsIds = array_column($emails, 'id');
        $relatedPhonesIds = array_column($phones, 'id');

        $ips = sentrion('models')->blacklistItems->getIpsRelatedToAccountWithinOperator($accountId, $apiKey);
        $relatedIpsIds = array_column($ips, 'id');
        if (count($relatedIpsIds) !== 0) {
            sentrion('models')->ip->updateFraudFlag($relatedIpsIds, $fraud, $apiKey);
        }

        $emails = sentrion('models')->blacklistItems->getEmailsRelatedToAccountWithinOperator($accountId, $apiKey);
        $relatedEmailsIds = array_column($emails, 'id');
        if (count($relatedEmailsIds) !== 0) {
            sentrion('models')->email->updateFraudFlag($relatedEmailsIds, $fraud, $apiKey);
        }

        $phones = sentrion('models')->blacklistItems->getPhonesRelatedToAccountWithinOperator($accountId, $apiKey);
        $relatedPhonesIds = array_column($phones, 'id');
        if (count($relatedPhonesIds) !== 0) {
            sentrion('models')->phone->updateFraudFlag($relatedPhonesIds, $fraud, $apiKey);
        }

        return array_merge($ips, $emails, $phones);
    }

    public function updateUserStatus(int $score, string $details, bool $cron, int $accountId, int $apiKey): void {
        $key = sentrion('models')->apiKeys->getKeyById($apiKey);
        $user = sentrion('models')->user->getUserById($accountId, $apiKey);

        $addToReview = $user['added_to_review'] === null && $user['fraud'] === null && $score <= $key['review_queue_threshold'];

        // update user score before blacklist processing
        sentrion('models')->user->updateUserStatus($score, $details, $addToReview, $accountId, $apiKey);

        if ($score <= $key['blacklist_threshold']) {
            $this->addToBlacklistQueue($accountId, true, true, false, $apiKey); // automatic blacklist anyway, do not recalculate
        } elseif (!$cron && $addToReview) {
            sentrion('controllers')->reviewQueue->setNotReviewedCount(false, $apiKey);           // do not use cache
        }

        sentrion('utils')->routes->callExtra('UPDATE_USER_STATUS', $score, $details, $addToReview, $cron, $accountId, $apiKey);
    }

    // only for event_account_score
    public function updateUserScore(array $scores, array $details, int $accountId, int $apiKey): void {
        sentrion('models')->userScore->updateUserScore($scores, $details, $accountId, $apiKey);
    }
}
