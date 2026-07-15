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

namespace Sentrion\Core;

class FileBasedPage extends Page {
    protected ?string $routeName = null;
    protected ?string $filePath = null;
    protected ?string $name = null;

    public function __construct() {
        $url = sentrion('request')->getPath();
        $filename = explode('/', trim($url, '/'))[0];

        // if file exists -> register route and use this file in index
        $path = dirname(__DIR__, 2) . '/assets/pages/' . $filename . '.php';

        sentrion('log')->debug('lookup file %s for URI %s.', $path, $url);

        if ($filename && $filename !== 'index' && file_exists($path) && preg_match('/^[A-Za-z0-9_-]+$/', $filename)) {
            $this->name = $filename;
            $this->routeName = '/' . $filename;
            $this->filePath = $path;
        }

        parent::__construct();
    }

    protected function getRoute(): string {
        return $this->routeName ?? '';
    }

    public function index(): void {
        if ($this->filePath) {
            $session    = sentrion('session');
            $request    = sentrion('request');
            $response   = sentrion('response');
            $sysop      = sentrion('sysop');
            $utils      = sentrion('utils');
            $page       = sentrion('page');
            $helpers    = sentrion('helpers');
            $db         = sentrion('db');
            $log        = sentrion('log');
            $user       = sentrion('user');
            $ip         = sentrion('ip');

            include_once $this->filePath;
        }
    }

    protected function init(): void {
        sentrion('page')->setTemplate($this->name . '.html');
        //name, title, template, js, authentication and roles should be set in route file
    }

    protected function uploadHelpers(): void {
        $path = 'assets/pages/views/';
        $ui = sentrion('storage')->get('UI');
        sentrion('storage')->set('UI', $ui . ';' . $path);

        // TODO: add dictionary management
        //sentrion('storage')->set(sentrion('page')->getName() . '_page_title', sentrion('page')->getTitle());

        $this->registerRouteOverrides($path . 'js/', '.js', 'application/javascript');
        $this->registerRouteOverrides($path . 'css/', '.css', null);
        $this->registerRouteOverrides($path . 'images/', '.svg', null);
    }

    public function beforeroute(): void {
        if (!sentrion('db')->initConnection()) {
            sentrion('log')->info('exit due database connection fail.');
            sentrion('response')->error(404);
        }

        sentrion('session')->extractCurrentOperator();

        sentrion('utils')->routes->callExtra('PAGE_BASE');

        $this->init();

        if (!sentrion('session')->get('csrf')) {
            sentrion('session')->set('csrf', bin2hex(random_bytes(16)));
        }

        $this->response = sentrion('request')->isAjax() ? (new \Sentrion\Views\Json()) : (new \Sentrion\Views\Frontend());
        $this->response->data = [];

        if (sentrion('session')->getCurrentOperator()) {
            $key = sentrion('session')->getCurrentKey();

            if (!$key) {
                sentrion('log')->info('redirect to /logout due to empty current key.');
                sentrion('response')->redirect('/logout');
            }

            $messages = sentrion('utils')->systemMessages->get($key->id);

            sentrion('storage')->set('SYSTEM_MESSAGES', $messages);
        }

        sentrion('page')->addParams($this->baseParams());
    }
}
