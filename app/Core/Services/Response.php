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

namespace Sentrion\Core\Services;

class Response {
    public function redirect(string $route = '/'): void {
        sentrion('router')->reroute($route);
        sentrion('log')->info('redirect %s to %s.', sentrion('request')->getUri(), $route);
    }

    public function error(int $code = 403): void {
        sentrion('router')->error($code);
        sentrion('log')->info('set error code %d on route %s.', $code, sentrion('request')->getUri());
    }

    public function redirectNotLoggedIn(string $route = '/'): void {
        if (sentrion('session')->getCurrentOperator()->isGuest()) {
            $this->redirect($route);
        }
    }

    public function redirectLoggedIn(string $route = '/'): void {
        if (sentrion('session')->getCurrentOperator()->isLoggedIn()) {
            $this->redirect($route);
        }
    }

    public function errorNotLoggedIn(int $code = 401): void {
        if (sentrion('session')->getCurrentOperator()->isGuest()) {
            $this->error($code);
        }
    }

    public function redirectProperRole(array $allowedRoles, array $blockedRoles = [], string $route = '/'): void {
        $roles = sentrion('session')->getCurrentOperator()->roles;

        if (count(array_intersect($roles, $allowedRoles)) && !count(array_intersect($roles, $blockedRoles))) {
            $this->redirect($route);
        }
    }

    public function redirectImproperRole(array $allowedRoles, array $blockedRoles = [], string $route = '/'): void {
        $roles = sentrion('session')->getCurrentOperator()->roles;

        if (count(array_intersect($roles, $blockedRoles))) {
            sentrion('log')->info('one of operators\' roles %s matches blocked roles list %s. redirect to %s.', json_encode($roles), json_encode($blockedRoles), $route);
            $this->redirect($route);
        }

        if (!count(array_intersect($roles, $allowedRoles))) {
            sentrion('log')->info('none of operators\' roles %s matches allowed roles list %s. redirect to %s.', json_encode($roles), json_encode($allowedRoles), $route);
            $this->redirect($route);
        }
    }

    public function errorImproperRole(array $allowedRoles, array $blockedRoles = [], int $code = 401): void {
        $roles = sentrion('session')->getCurrentOperator()->roles;

        if (count(array_intersect($roles, $blockedRoles))) {
            sentrion('log')->info('one of operators\' roles %s matches blocked roles list %s. raise error %d.', json_encode($roles), json_encode($blockedRoles), $code);
            $this->error($code);
        }

        if (!count(array_intersect($roles, $allowedRoles))) {
            sentrion('log')->info('none of operators\' roles %s matches allowed roles list %s. raise error %d.', json_encode($roles), json_encode($allowedRoles), $code);
            $this->error($code);
        }
    }
}
