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

namespace Sentrion\Entities;

class Operator {
    // TODO: should be private and immutable, should provide getters for operatorFields
    public int $id;
    public string $email;
    public ?string $password;
    public ?string $firstname;
    public ?string $lastname;
    //public ?int $isActive;
    //public ?int $isClosed;
    public ?string $activationKey;
    //public ?string $createdAt;
    public string $timezone;
    public ?int $reviewQueueCnt;
    public ?string $reviewQueueUpdatedAt;
    public ?string $lastEventTime;
    public string $reminderFreq;
    //public ?string $lastUnreviewedItemsReminderFreq;
    public ?int $blacklistUsersCnt;
    public array $roles;            // [string]
    public array $permissions;      // [int]

    // TODO: do we need isOwner?
    public function __construct(
        int $id,
        string $email,
        ?string $password,
        ?string $firstname,
        ?string $lastname,
        ?string $activationKey,
        string $timezone,
        ?int $reviewQueueCnt,
        ?string $reviewQueueUpdatedAt,
        ?string $lastEventTime,
        string $reminderFreq,
        ?int $blacklistUsersCnt,
        array $rolesPermissions,
    ) {
        $this->id                   = $id;
        $this->email                = $email;
        $this->password             = $password;
        $this->firstname            = $firstname;
        $this->lastname             = $lastname;
        $this->activationKey        = $activationKey;
        $this->timezone             = $timezone;
        $this->reviewQueueCnt       = $reviewQueueCnt;
        $this->reviewQueueUpdatedAt = $reviewQueueUpdatedAt;
        $this->lastEventTime        = $lastEventTime;
        $this->reminderFreq         = $reminderFreq;
        $this->blacklistUsersCnt    = $blacklistUsersCnt;
        $this->roles                = array_keys($rolesPermissions);
        $this->permissions          = array_unique(array_column(array_merge(...array_values($rolesPermissions)), 'permission_value'));
    }

    public static function getById(int $operatorId): self {
        $operator = sentrion('models')->operator->getOperatorById($operatorId);

        if (!$operator) {
            $operator = sentrion('models')->operator->getOperatorById(sentrion('utils')->constants->GUEST_OPERATOR_ID);
        }

        $rolesPermissions = sentrion('utils')->operatorAccess->getRolesWithPermissions($operator['id']);

        return new self(
            $operator['id'],
            $operator['email'],
            $operator['password'],
            $operator['firstname'],
            $operator['lastname'],
            $operator['activation_key'],
            $operator['timezone'],
            $operator['review_queue_cnt'],
            $operator['review_queue_updated_at'],
            $operator['last_event_time'],
            $operator['unreviewed_items_reminder_freq'],
            $operator['blacklist_users_cnt'],
            $rolesPermissions
        );
    }

    public function addRole(string $role): void {
        sentrion('utils')->operatorAccess->addOperatorRole($role, $this->id);
    }

    public function hasRole(string $role): bool {
        return sentrion('utils')->operatorAccess->operatorHasRole($role, $this->id);
    }

    public function removeRole(string $role): void {
        sentrion('utils')->operatorAccess->removeOperatorRole($role, $this->id);
    }

    public function getRoles(): array {
        return sentrion('utils')->operatorAccess->getRoles($this->id);
    }

    public function hasPermission(int $permission): bool {
        return sentrion('utils')->operatorAccess->hasPermission($permission, $this->id);
    }

    public function isSuperuser(): bool {
        return in_array('superuser', $this->roles);
    }

    public function isGuest(): bool {
        return $this->id === sentrion('utils')->constants->GUEST_OPERATOR_ID;
    }

    public function isLoggedIn(): bool {
        return !$this->isGuest();
    }

    public function viewable(string $pageValue): bool {
        return sentrion('utils')->operatorAccess->viewable($pageValue, $this->id);
    }

    public function editable(string $pageValue): bool {
        return sentrion('utils')->operatorAccess->editable($pageValue, $this->id);
    }

    public function deleteable(string $pageValue): bool {
        return sentrion('utils')->operatorAccess->deleteable($pageValue, $this->id);
    }

    public function publishable(string $pageValue): bool {
        return sentrion('utils')->operatorAccess->publishable($pageValue, $this->id);
    }

    public function adminable(string $pageValue): bool {
        return sentrion('utils')->operatorAccess->adminable($pageValue, $this->id);
    }
}
