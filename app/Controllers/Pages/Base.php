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

abstract class Base {
    protected \Sentrion\Views\Base $response;

    protected string $page;
    protected ?object $controller = null;
    protected \Sentrion\Entities\Operator $operator;
    protected ?int $apiKey = null;
    protected ?int $id = null;
    protected bool $allowGuest = false;

    protected string $classname = '';

    public function __construct() {
        $timer = sentrion('request')->setTimer();

        $keepSessionInDb = sentrion('storage')->get('KEEP_SESSION_IN_DB') ?? null;
        if (!sentrion('utils')->database->initConnect(boolval($keepSessionInDb))) {
            sentrion('response')->error(404);
        }

        //Determine current user
        sentrion('utils')->routes->setCurrentRequestOperator();
        sentrion('utils')->routes->setCurrentRequestApiKey();

        $this->operator     = sentrion('utils')->routes->getCurrentRequestOperator();
        $this->apiKey       = sentrion('utils')->apiKeys->getCurrentOperatorApiKeyId();
        $this->id           = sentrion('utils')->conversion->getIntRequestParam('id', true);

        $parts = explode('\\', static::class);
        $this->classname = $parts[count($parts) - 1];

        //$this->page         = sentrion('pages')->getByClassName($this->classname);
        $this->controller   = sentrion('controllers')->getByClassName($this->classname);

        if (!sentrion('session')->get('csrf')) {
            // Set anti-CSRF token.
            sentrion('session')->set('csrf', bin2hex(random_bytes(16)));
        }

        sentrion('storage')->set('CSRF', sentrion('session')->get('csrf'));
        sentrion('utils')->routes->callExtra('PAGE_BASE');

        if (!$this->isAllowed()) {
            $this->notAllowed();
        }

        sentrion('log')->debug('page %s construct finished in %f.', static::class, sentrion('request')->getTimer($timer));
    }

    protected function isAllowed(): bool {
        return ($this->allowGuest && $this->operator->isGuest()) || (!$this->allowGuest && !$this->operator->isGuest());
    }

    protected function notAllowed(): void {
        if (sentrion('request')->isAjax()) {
            sentrion('response')->error(404);
        }

        if (!$this->allowGuest && $this->operator->isGuest()) {
            sentrion('response')->redirect('/login');
        }

        if ($this->allowGuest && !$this->operator->isGuest()) {
            sentrion('response')->redirect('/');
        }
    }

    public function showIndexPage(): \Sentrion\Views\Frontend {
        $response = new \Sentrion\Views\Frontend();
        $response->data = [];

        if ($this->page) {
            $response->data = sentrion('utils')->render->applyPageParams($this->getPageParams(), $this->page);
        }

        return $response;
    }

    protected function getPageParams(): array {
        return [];
    }

    public function assertCanView(): void {
        if (!$this->operator->viewable($this->page)) {
            sentrion('response')->error(403);
        }
    }

    public function assertCanEdit(): void {
        if (!$this->operator->editable($this->page)) {
            sentrion('response')->error(403);
        }
    }

    public function assertCanDelete(): void {
        if (!$this->operator->deleteable($this->page)) {
            sentrion('response')->error(403);
        }
    }

    public function assertCanPublish(): void {
        if (!$this->operator->publishable($this->page)) {
            sentrion('response')->error(403);
        }
    }

    public function assertCanAdmin(): void {
        if (!$this->operator->adminable($this->page)) {
            sentrion('response')->error(403);
        }
    }
}
