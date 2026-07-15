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

abstract class Page {
    protected \Sentrion\Views\Base $response;

    public function __construct() {
        $this->registerRoute();
    }

    abstract protected function init(): void;

    abstract protected function getRoute(): string;

    public function registerRoute(): void {
        $route = $this->getRoute();
        if ($route) {
            $routeDef = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS ' . $route;
            $routeDest = get_class($this) . '->index';
            sentrion('router')->route($routeDef, $routeDest);
        }
    }

    protected function uploadHelpers(): void {
        $path = 'assets/pages/' . lcfirst(sentrion('page')->getName());

        // load dictionary file if present
        $dictionary = dirname(__DIR__, 2) . $path . '/dictionary.php';
        if (file_exists($dictionary)) {
            $values = include $dictionary;

            if ($values !== false) {
                foreach ($values as $key => $value) {
                    sentrion('storage')->set($key, $value);
                }
            }
        }

        $path .= '/ui/';
        $ui = sentrion('storage')->get('UI');
        //sentrion('storage')->set('UI', $ui . ';' . $path . 'templates/');
        sentrion('storage')->set('UI', $ui . ';' . $path . 'templates/');

        // TODO: add dictionary management
        sentrion('storage')->set(sentrion('page')->getName() . '_page_title', sentrion('page')->getTitle());

        $this->registerRouteOverrides($path . 'js/', '.js', 'application/javascript');
        $this->registerRouteOverrides($path . 'css/', '.css', null);
        $this->registerRouteOverrides($path . 'images/', '.svg', null);
    }

    protected function registerRouteOverrides(string $dir, string $extension, ?string $contentType = null): void {
        if (!is_dir($dir)) {
            return;
        }

        $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

        $root = rtrim(realpath($dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $len = strlen($root);
        $errorCode = 404;
        foreach ($iter as $file) {
            if ($file->isFile() && $file->getExtension() === $extension) {
                $relative = substr($file->getPathname(), $len);
                $path = $dir . $relative;

                sentrion('request')->registerRoute('GET', '/ui/' . $relative, function () use ($path, $contentType, $errorCode) {
                    $file = $path;
                    if (file_exists($file)) {
                        $contentType = $contentType === null ? mime_content_type($file) : $contentType;
                        header('Content-Type: ' . $contentType);
                        readfile($file);
                    } else {
                        sentrion('response')->error($errorCode);
                    }
                });
            }
        }
    }

    // allow user to perform their own request type logic
    public function index(): void {
        sentrion('response')->error(403);
    }

    public function beforeroute(): void {
        if (sentrion('request')->isAjax()) {
            $this->response = new \Sentrion\Views\Json();

            if (!sentrion('db')->initConnection()) {
                sentrion('log')->info('exit due to database connection fail.');
                sentrion('response')->error(404);
            }
            sentrion('session')->extractCurrentOperator();

            sentrion('utils')->routes->callExtra('PAGE_BASE');

            $this->init();

            $errorCode = sentrion('request')->validateCsrf();
            if ($errorCode) {
                sentrion('log')->info('exit due to CSRF token mismatch.');
                sentrion('resonse')->error(403);
            }

            if (sentrion('page')->getAuthenticated()) {
                sentrion('response')->errorNotLoggedIn();
                sentrion('response')->errorImproperRole(sentrion('page')->getAllowedRoles(), sentrion('page')->getBlockedRoles());
            }

            return;
        }

        $this->init();

        if (!sentrion('session')->get('csrf')) {
            sentrion('session')->set('csrf', bin2hex(random_bytes(16)));
        }

        $this->response = new \Sentrion\Views\Frontend();
        $this->response->data = [];

        if (!sentrion('db')->initConnection()) {
            sentrion('log')->info('exit due to database connection fail.');
            sentrion('response')->error(404);
        }

        sentrion('session')->extractCurrentOperator();

        sentrion('utils')->routes->callExtra('PAGE_BASE');

        $verb = sentrion('request')->getRequestType();
        if (sentrion('request')->isCli()) {
            sentrion('log')->info('request is initiated from command line.');
            sentrion('response')->error(403);
        }

        if ($verb !== 'GET' && !sentrion('request')->validateCsrf()) {
            sentrion('log')->info('form provided invalid CSRF token.');
            sentrion('response')->error(403);
        }

        if (sentrion('page')->getAuthenticated()) {
            sentrion('response')->redirectNotLoggedIn();
            sentrion('response')->redirectImproperRole(sentrion('page')->getAllowedRoles(), sentrion('page')->getBlockedRoles());
        }

        if (sentrion('page')->getAuthenticated() && sentrion('session')->getCurrentOperator()->isLoggedIn()) {
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

    public function afterroute(): void {
        if (!sentrion('request')->isAjax()) {
            $this->uploadHelpers();
            $this->updateAndSetParams();
        }

        $this->response->data = sentrion('page')->getParams();

        sentrion('log')->logSqlIfPossible();

        echo $this->response->render();
    }

    private function updateAndSetParams(): void {
        $data = sentrion('page')->getParams();

        $code = $data['ERROR_CODE'] ?? null;
        if ($code && is_int($code)) {
            $data['ERROR_MESSAGE'] = sentrion('storage')->get('error_' . strval($code));
        }

        $code = $data['SUCCESS_CODE'] ?? null;
        if ($code && is_int($code)) {
            $data['SUCCESS_MESSAGE'] = sentrion('storage')->get('error_' . strval($code));
        }

            $time = sentrion('utils')->nowUtc();
        if (array_key_exists('ERROR_MESSAGE', $data)) {
            $data['ERROR_MESSAGE_TIMESTAMP'] = $time;
        }

        if (array_key_exists('SUCCESS_MESSAGE', $data)) {
            $data['SUCCESS_MESSAGE_TIMESTAMP'] = $time;
        }

        $code = sentrion('session')->get('extra_message_code');
        if ($code !== null) {
            sentrion('session')->remove('extra_message_code');

            if (!isset($data['SYSTEM_MESSAGES'])) {
                $data['SYSTEM_MESSAGES'] = [];
            }

            $data['SYSTEM_MESSAGES'][] = [
                'text'          => sentrion('storage')->get('error_' . $code),
                'created_at'    => $time,
            ];
        }

        $data = sentrion('utils')->routes->callExtra('APPLY_PAGE_PARAMS', $data, sentrion('page')->getName()) ?? $data;

        sentrion('page')->setParams($data);
    }

    protected function baseParams(): array {
        $time = sentrion('utils')->nowForCurrentOperator();

        $title = sentrion('storage')->get(sentrion('page')->getName() . '_page_title') ?: sentrion('utils')->constants->UNAUTHORIZED_USERID;
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $pageTitle = $safeTitle . ' '  . sentrion('utils')->constants->PAGE_TITLE_POSTFIX;

        $operator = sentrion('session')->getCurrentOperator();

        $params = [
            'PAGE_TITLE'                    => $pageTitle,
            'BREADCRUMB_TITLE'              => sentrion('storage')->get(sentrion('page')->getName() . '_breadcrumb_title') ?? '',
            'CURRENT_PATH'                  => sentrion('request')->getPath(),
            'CURRENT_PATTERN'               => sentrion('request')->getPattern(),
            'ALLOW_EMAIL_PHONE'             => sentrion('utils')->variables->getEmailPhoneAllowed(),
            'CSRF'                          => sentrion('session')->get('csrf'),
            'NOT_REVIEWED_USERS_CNT'        => sentrion('utils')->conversion->formatKiloValue($operator->reviewQueueCnt ?? 0),
            'BLACKLIST_USERS_CNT'           => sentrion('utils')->conversion->formatKiloValue($operator->blacklistUsersCnt ?? 0),
            'FILE_PAGES'                    => sentrion('assets')->pages->getMenuPages(),
            'HTML_FILE'                     => sentrion('page')->getTemplate(),
            'JS'                            => sentrion('page')->getJavascript(),
            'INTERNAL_PAGE'                 => sentrion('page')->getAuthenticated(),
            'LOAD_ACCEPT_LANGUAGE_PARSER'   => true,
            'LOAD_AUTOCOMPLETE'             => true,
            'LOAD_CHOICES'                  => true,
            'LOAD_DATATABLE'                => true,
            'LOAD_JVECTORMAP'               => true,
            'LOAD_UPLOT'                    => true,
        ];

        if ($operator) {
            $cnt = $operator->reviewQueueCnt ?? 0;
            $params['NUMBER_OF_NOT_REVIEWED_USERS'] = sentrion('utils')->conversion->formatKiloValue($cnt);

            $cnt = $operator->blacklistUsersCnt ?? 0;
            $params['NUMBER_OF_BLACKLIST_USERS'] = sentrion('utils')->conversion->formatKiloValue($cnt);

            $params += sentrion('controllers')->main->getCurrentTime($operator);
        }

        return $params;
    }
}
