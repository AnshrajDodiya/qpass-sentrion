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

namespace Sentrion\Controllers;

class Data {
    protected \Sentrion\Views\Base $response;

    //protected ?object $page = null;
    protected ?string $page = null;
    protected ?object $controller = null;
    protected ?\Sentrion\Entities\Operator $operator = null;
    protected ?int $apiKey = null;
    protected ?int $id = null;

    protected array $data = [];

    protected string $classname = '';

    protected int $timer;

    public function __construct() {
        $this->timer = sentrion('request')->setTimer();

        $keepSessionInDb = sentrion('storage')->get('KEEP_SESSION_IN_DB') ?? null;
        if (!sentrion('utils')->database->initConnect(boolval($keepSessionInDb))) {
            sentrion('response')->error(404);
        }

        //Determine current user
        sentrion('utils')->routes->setCurrentRequestOperator();
        sentrion('utils')->routes->setCurrentRequestApiKey();

        $parts = explode('\\', static::class);
        $this->classname = $parts[count($parts) - 1];

        sentrion('log')->debug('ajax navigation construct for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($this->timer));
    }

    public function beforeroute(): void {
        $operator     = sentrion('utils')->routes->getCurrentRequestOperator();
        sentrion('log')->debug('operator %s with roles %s accessing ajax %s', $operator->email, json_encode($operator->roles), sentrion('request')->getUri());

        $timer = sentrion('request')->setTimer();

        if (!sentrion('request')->isAjax()) {
            sentrion('response')->error(403);
        }

        $errorCode = sentrion('request')->validateCsrf();
        if ($errorCode) {
            sentrion('log')->info('ajax request with invalid CSRF %s.', sentrion('request')->getUri());
            sentrion('response')->error(403);
        }

        sentrion('response')->redirectNotLoggedIn();

        sentrion('log')->debug('ajax navigation beforeroute for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($timer));
    }

    public function afterroute(): void {
        $timer = sentrion('request')->setTimer();

        $shouldPrintSqlToLog = sentrion('storage')->get('PRINT_SQL_LOG_AFTER_EACH_SCRIPT_CALL');

        if ($shouldPrintSqlToLog) {
            $log = sentrion('utils')->database->getDb()->log();
            if ($log) {
                sentrion('utils')->logger->logSql(sentrion('request')->getPath(), $log);
            }
        }

        $response = new \Sentrion\Views\Json();
        $response->data = $this->data;

        echo $response->render();

        sentrion('log')->debug('ajax navigation afterroute for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($timer));
        sentrion('log')->debug('whole ajax route processing for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($this->timer));
    }

    public function getEventsList(): void {
        $this->data = sentrion('pages')->events->getList();
    }

    public function getUsersList(): void {
        $this->data = sentrion('pages')->users->getList();
    }

    public function getReviewQueueList(): void {
        $this->data = sentrion('pages')->reviewQueue->getList();
    }

    public function getBlacklistList(): void {
        $this->data = sentrion('pages')->blacklist->getList();
    }

    public function getDevicesList(): void {
        $this->data = sentrion('pages')->devices->getList();
    }

    public function getUserAgentsList(): void {
        $this->data = sentrion('pages')->userAgents->getList();
    }

    public function getResourcesList(): void {
        $this->data = sentrion('pages')->resources->getList();
    }

    public function getPhonesList(): void {
        $this->data = sentrion('pages')->phones->getList();
    }

    public function getFieldAuditTrailList(): void {
        $this->data = sentrion('pages')->fields->getTrailList();
    }

    public function getFieldAuditsList(): void {
        $this->data = sentrion('pages')->fields->getList();
    }

    public function getIspsList(): void {
        $this->data = sentrion('pages')->isps->getList();
    }

    public function getIpsList(): void {
        $this->data = sentrion('pages')->ips->getList();
    }

    public function getEmailsList(): void {
        $this->data = sentrion('pages')->emails->getList();
    }

    public function getDomainsList(): void {
        $this->data = sentrion('pages')->domains->getList();
    }

    public function getLogbookList(): void {
        $this->data = sentrion('pages')->logbook->getList();
    }

    public function getUsageStats(): void {
        $this->data = sentrion('pages')->api->getUsageStats();
    }

    public function getCountriesList(): void {
        $this->data = sentrion('pages')->countries->getList();
    }

    public function getRulesList(): void {
        $this->data = sentrion('pages')->rules->getList();
    }

    public function getEventsChart(): void {
        $this->data = sentrion('pages')->events->getChart();
    }

    public function getUsersChart(): void {
        $this->data = sentrion('pages')->users->getChart();
    }

    public function getReviewQueueChart(): void {
        $this->data = sentrion('pages')->reviewQueue->getChart();
    }

    public function getBlacklistChart(): void {
        $this->data = sentrion('pages')->blacklist->getChart();
    }

    public function getDevicesChart(): void {
        $this->data = sentrion('pages')->devices->getChart();
    }

    public function getUserAgentsChart(): void {
        $this->data = sentrion('pages')->userAgents->getChart();
    }

    public function getResourcesChart(): void {
        $this->data = sentrion('pages')->resources->getChart();
    }

    public function getPhonesChart(): void {
        $this->data = sentrion('pages')->phones->getChart();
    }

    public function getFieldAuditsChart(): void {
        $this->data = sentrion('pages')->fields->getChart();
    }

    public function getIspsChart(): void {
        $this->data = sentrion('pages')->isps->getChart();
    }

    public function getIpsChart(): void {
        $this->data = sentrion('pages')->ips->getChart();
    }

    public function getDomainsChart(): void {
        $this->data = sentrion('pages')->domains->getChart();
    }

    public function getLogbookChart(): void {
        $this->data = sentrion('pages')->logbook->getChart();
    }

    public function getUserSparklinesChart(): void {
        $this->data = sentrion('pages')->user->getSparklinesChart();
    }

    public function getIpsTimeFrameTotal(): void {
        $this->data = sentrion('pages')->ips->getTimeFrameTotal();
    }

    public function getIspsTimeFrameTotal(): void {
        $this->data = sentrion('pages')->isps->getTimeFrameTotal();
    }

    public function getDomainsTimeFrameTotal(): void {
        $this->data = sentrion('pages')->domains->getTimeFrameTotal();
    }

    public function getCountriesTimeFrameTotal(): void {
        $this->data = sentrion('pages')->countries->getTimeFrameTotal();
    }

    public function getResourcesTimeFrameTotal(): void {
        $this->data = sentrion('pages')->resources->getTimeFrameTotal();
    }

    public function getFieldAuditsTimeFrameTotal(): void {
        $this->data = sentrion('pages')->fields->getTimeFrameTotal();
    }

    public function getUserAgentsTimeFrameTotal(): void {
        $this->data = sentrion('pages')->userAgents->getTimeFrameTotal();
    }

    public function getEmailDetails(): void {
        $this->data = sentrion('pages')->emails->getEmailDetails();
    }

    public function getEventDetails(): void {
        $this->data = sentrion('pages')->events->getEventDetails();
    }

    public function getFieldEventDetails(): void {
        $this->data = sentrion('pages')->fields->getFieldEventDetails();
    }

    public function getPhoneDetails(): void {
        $this->data = sentrion('pages')->phones->getPhoneDetails();
    }

    public function getDeviceDetails(): void {
        $this->data = sentrion('pages')->devices->getDeviceDetails();
    }

    public function getLogbookDetails(): void {
        $this->data = sentrion('pages')->logbook->getLogbookDetails();
    }

    public function getNotCheckedEntitiesCount(): void {
        $this->data = sentrion('pages')->api->getNotCheckedEntitiesCount();
    }

    public function getDomainDetails(): void {
        $this->data = sentrion('pages')->domain->getDomainDetails();
    }

    public function getUserAgentDetails(): void {
        $this->data = sentrion('pages')->userAgent->getUserAgentDetails();
    }

    public function getIspDetails(): void {
        $this->data = sentrion('pages')->isp->getIspDetails();
    }

    public function getIpDetails(): void {
        $this->data = sentrion('pages')->ip->getIpDetails();
    }

    public function getUserDetails(): void {
        $this->data = sentrion('pages')->user->getUserDetails();
    }

    public function saveRule(): void {
        $this->data = sentrion('pages')->rules->saveRule();
    }

    public function removeFromBlacklist(): void {
        $this->data = sentrion('pages')->blacklist->removeFromBlacklist();
    }

    public function removeFromWatchlist(): void {
        $this->data = sentrion('pages')->watchlist->removeFromWatchlist();
    }

    public function enrichPhoneEntity(): void {
        $this->data = sentrion('pages')->phones->enrichEntity();
    }

    public function enrichEmailEntity(): void {
        $this->data = sentrion('pages')->emails->enrichEntity();
    }

    public function manageUser(): void {
        $this->data = sentrion('pages')->user->manageUser();
    }

    public function reviewUser(): void {
        $this->data = sentrion('pages')->reviewQueue->reviewUser();
    }

    public function getTopTen(): void {
        $this->data = sentrion('pages')->dashboard->getTopTen();
    }

    public function getCurrentTime(): void {
        $this->data = sentrion('pages')->main->getCurrentTime();
    }

    public function getConstants(): void {
        $this->data = sentrion('pages')->main->getConstants();
    }

    public function getSearchResults(): void {
        $this->data = sentrion('pages')->main->getSearchResults();
    }

    public function checkRule(): void {
        $this->data = sentrion('pages')->rules->checkRule();
    }

    public function getUserScoreDetails(): void {
        $this->data = sentrion('pages')->user->getUserScoreDetails();
    }

    public function getDashboardStat(): void {
        $this->data = sentrion('pages')->dashboard->getDashboardStat();
    }

    public function getMap(): void {
        $this->data = sentrion('pages')->countries->getMap();
    }

    public function getReviewUsersQueueCount(): void {
        $this->data = sentrion('pages')->reviewQueue->setNotReviewedCount(false);
    }

    public function getBlacklistUsersCount(): void {
        $this->data = sentrion('pages')->blacklist->setBlacklistUsersCount(false);
    }
}
