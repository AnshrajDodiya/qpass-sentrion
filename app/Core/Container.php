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

class Container {
    private function __construct() {
    }

    private static array $services = [
        'session'       => \Sentrion\Core\Services\Session::class,
        'request'       => \Sentrion\Core\Services\Request::class,
        'response'      => \Sentrion\Core\Services\Response::class,

        'sysop'         => \Sentrion\Core\Services\Sysop::class,

        'storage'       => \Sentrion\Core\Services\Storage::class,
        'page'          => \Sentrion\Core\Services\Page::class,

        'helpers'       => \Sentrion\Core\Services\Helpers::class,

        'db'            => \Sentrion\Core\Services\Db::class,
        'log'           => \Sentrion\Core\Services\Log::class,
        'rule'          => \Sentrion\Core\Services\Rule::class,
        'user'          => \Sentrion\Core\Services\User::class,
        'ip'            => \Sentrion\Core\Services\Ip::class,
        'resource'      => \Sentrion\Core\Services\Resource::class,

        'rules'         => \Sentrion\Core\Services\Rules::class,
        'users'         => \Sentrion\Core\Services\Users::class,
        'ips'           => \Sentrion\Core\Services\Ips::class,
        'resources'     => \Sentrion\Core\Services\Resources::class,

        'router'        => \Sentrion\Core\Services\Router::class,

        'controllers'   => \Sentrion\Core\Services\Controllers::class,
        'pages'         => \Sentrion\Core\Services\Pages::class,
        'models'        => \Sentrion\Core\Services\Models::class,
        'utils'         => \Sentrion\Core\Services\Utils::class,
        'grids'         => \Sentrion\Core\Services\Grids::class,
        'charts'        => \Sentrion\Core\Services\Charts::class,
        'assets'        => \Sentrion\Core\Services\Assets::class,
        'entities'      => \Sentrion\Core\Services\Entities::class,
        'queries'       => \Sentrion\Core\Services\Queries::class,
    ];

    private static array $renewable = [
        'users',
        'ips',
        'resources'
    ];

    private static array $instances = [];

    public static function resolve(string $name): object {
        if (!isset(self::$services[$name])) {
            throw new \Exception('Validation failed');
        }

        if (in_array($name, self::$renewable)) {
            return new self::$services[$name]();
        }

        if (!isset(self::$instances[$name])) {
            if ($name === 'router') {
                self::$instances[$name] = sentrion('utils')->router->get();
            } else {
                self::$instances[$name] = new self::$services[$name]();
            }
        }

        return self::$instances[$name];
    }
}
