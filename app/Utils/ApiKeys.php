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

namespace Sentrion\Utils;

class ApiKeys {
    public static function getCurrentOperatorApiKeyId(): ?int {
        $key = sentrion('utils')->routes->getCurrentRequestApiKey();

        return $key ? $key->id : null;
    }

    public static function getCurrentOperatorApiKeyString(): ?string {
        $key = sentrion('utils')->routes->getCurrentRequestApiKey();

        return $key ? $key->key : null;
    }

    public static function getCurrentOperatorEnrichmentKeyString(): ?string {
        $key = sentrion('utils')->routes->getCurrentRequestApiKey();

        return $key ? $key->token : null;
    }

    public static function getOperatorApiKeys(int $operatorId): array {
        $apiKeys = sentrion('models')->apiKeys->getKeys($operatorId);

        $isOwner = true;
        if (!$apiKeys) {
            $keyId = sentrion('models')->apiKeyCoOwner->getCoOwnershipKeyId($operatorId);

            if ($keyId) {
                $isOwner = false;
                $apiKeys[] = sentrion('models')->apiKeys->getKeyById($keyId);
            }
        }

        return [$isOwner, $apiKeys];
    }

    public static function getFirstKeyByOperatorId(int $operatorId): ?int {
        $apiKeys = sentrion('models')->apiKeys->getKeys($operatorId);

        if (!$apiKeys) {
            $keyId = sentrion('models')->apiKeyCoOwner->getCoOwnershipKeyId($operatorId);

            if ($keyId) {
                $apiKeys[] = sentrion('models')->apiKeys->getKeyById($keyId);
            }
        }

        return $apiKeys[0]['id'] ?? null;
    }
}
