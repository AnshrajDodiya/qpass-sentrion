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

class Navigation {
    protected \Sentrion\Views\Base $response;

    //protected ?object $page = null;
    protected ?string $page = null;
    protected ?object $controller = null;
    protected ?\Sentrion\Entities\Operator $operator = null;
    protected ?int $apiKey = null;
    protected ?int $id = null;

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

        $this->operator = sentrion('utils')->routes->getCurrentRequestOperator();
        $this->apiKey = sentrion('utils')->routes->getCurrentRequestApiKey()?->id;

        sentrion('log')->debug('navigation construct for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($this->timer));
    }

    public function beforeroute(): void {
        sentrion('log')->debug('operator %s with roles %s accessing page %s', $this->operator->email, json_encode($this->operator->roles), sentrion('request')->getUri());

        $timer = sentrion('request')->setTimer();

        if (sentrion('request')->isAjax()) {
            sentrion('response')->error(403);
        }

        if ($this->operator->isLoggedIn()) {
            sentrion('utils')->updates->syncUpdates();

            if (!$this->apiKey) {
                sentrion('log')->debug('redirect to logout from route %s.', sentrion('request')->getUri());
                sentrion('response')->redirect('/logout');
            }

            $messages = sentrion('utils')->systemMessages->get($this->apiKey);

            sentrion('storage')->set('SYSTEM_MESSAGES', $messages);
        }

        sentrion('log')->debug('navigation beforeroute for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($timer));
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

        echo $this->response->render();

        sentrion('log')->debug('navigation afterroute for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($timer));
        sentrion('log')->debug('whole route processing for %s in %f.', sentrion('request')->getUri(), sentrion('request')->getTimer($this->timer));
    }

    public function getSignupPage(): void {
        $this->response = sentrion('pages')->signup->showIndexPage();
    }

    public function getLoginPage(): void {
        $this->response = sentrion('pages')->login->showIndexPage();
    }

    public function getLogoutPage(): void {
        $this->response = sentrion('pages')->logout->showIndexPage();
    }

    public function getForgotPasswordPage(): void {
        $this->response = sentrion('pages')->forgotPassword->showIndexPage();
    }

    public function getPassworRecoveringPage(): void {
        $this->response = sentrion('pages')->passwordRecovering->showIndexPage();
    }

    public function getHomePage(): void {
        $this->response = sentrion('pages')->dashboard->showIndexPage();
    }

    public function getEventsPage(): void {
        $this->response = sentrion('pages')->events->showIndexPage();
    }

    public function getReviewQueuePage(): void {
        $this->response = sentrion('pages')->reviewQueue->showIndexPage();
    }

    public function getBlacklistPage(): void {
        $this->response = sentrion('pages')->blacklist->showIndexPage();
    }

    public function getLogbookPage(): void {
        $this->response = sentrion('pages')->logbook->showIndexPage();
    }

    public function getWatchlistPage(): void {
        $this->response = sentrion('pages')->watchlist->showIndexPage();
    }

    public function getApiPage(): void {
        $this->response = sentrion('pages')->api->showIndexPage();
    }

    public function getRulesPage(): void {
        $this->response = sentrion('pages')->rules->showIndexPage();
    }

    public function getSettingsPage(): void {
        $this->response = sentrion('pages')->settings->showIndexPage();
    }

    public function getManualCheckPage(): void {
        $this->response = sentrion('pages')->manualCheck->showIndexPage();
    }

    public function getUserPage(): void {
        $this->response = sentrion('pages')->user->showIndexPage();
    }

    public function getUserAgentPage(): void {
        $this->response = sentrion('pages')->userAgent->showIndexPage();
    }

    public function getIpPage(): void {
        $this->response = sentrion('pages')->ip->showIndexPage();
    }

    public function getDomainPage(): void {
        $this->response = sentrion('pages')->domain->showIndexPage();
    }

    public function getUsersPage(): void {
        $this->response = sentrion('pages')->users->showIndexPage();
    }

    public function getUserAgentsPage(): void {
        $this->response = sentrion('pages')->userAgents->showIndexPage();
    }

    public function getIpsPage(): void {
        $this->response = sentrion('pages')->ips->showIndexPage();
    }

    public function getIspsPage(): void {
        $this->response = sentrion('pages')->isps->showIndexPage();
    }

    public function getIspPage(): void {
        $this->response = sentrion('pages')->isp->showIndexPage();
    }

    public function getCountryPage(): void {
        $this->response = sentrion('pages')->country->showIndexPage();
    }

    public function getCountriesPage(): void {
        $this->response = sentrion('pages')->countries->showIndexPage();
    }

    public function getDomainsPage(): void {
        $this->response = sentrion('pages')->domains->showIndexPage();
    }

    public function getResourcesPage(): void {
        $this->response = sentrion('pages')->resources->showIndexPage();
    }

    public function getResourcePage(): void {
        $this->response = sentrion('pages')->resource->showIndexPage();
    }

    public function getFieldAuditsPage(): void {
        $this->response = sentrion('pages')->fields->showIndexPage();
    }

    public function getFieldAuditPage(): void {
        $this->response = sentrion('pages')->field->showIndexPage();
    }
}
