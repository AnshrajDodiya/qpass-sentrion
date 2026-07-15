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

abstract class Base {
    public function __construct() {
        $keepSessionInDb = sentrion('storage')->get('KEEP_SESSION_IN_DB') ?? null;
        if (!sentrion('utils')->database->initConnect(boolval($keepSessionInDb))) {
            sentrion('response')->error(404);
        }

        //Determine current user
        sentrion('utils')->routes->setCurrentRequestOperator();
        sentrion('utils')->routes->setCurrentRequestApiKey();

        //Set CSRF token
        //$rnd = mt_rand();
        //sentrion('router')->CSRF = sprintf('%s.%s', sentrion('router')->SEED, sentrion('router')->hash($rnd));
    }

    protected function idMapIterate(array $map, object $model, int $apiKey, ?string $default = 'getAll', mixed ...$extra): array {
        $result = [];

        foreach ($map as $param => $method) {
            $id = sentrion('utils')->conversion->getIntRequestParam($param, true);
            if ($id !== null) {
                $result = $model->$method($id, $apiKey, ...$extra);
            }

            if ($result) {
                break;
            }
        }

        if (!$result && $default !== null) {
            $result = $model->$default($apiKey, ...$extra);
        }

        return $result;
    }
}
