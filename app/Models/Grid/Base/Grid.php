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

namespace Sentrion\Models\Grid\Base;

abstract class Grid extends \Sentrion\Models\Base {
    protected ?object $idsModel = null;
    protected ?object $queryModel = null;

    public function __construct() {
        $parts = explode('\\', static::class);
        $classname = $parts[count($parts) - 2];

        $this->idsModel = new ('\\Sentrion\\Models\\Grid\\' . $classname . '\\Ids')();
        $this->queryModel = new ('\\Sentrion\\Models\\Grid\\' . $classname . '\\Query')();
    }

    protected function getGrid(int $apiKey, ?string $ids = null, array $idsParams = []): array {
        $this->setIds($ids, $idsParams, $apiKey);

        $draw = sentrion('request')->getRequestParam('draw') ?? 1;
        $data = $this->getData();
        $total = $this->getTotal();

        $dateRange = sentrion('utils')->dateRange->getDatesRangeFromRequest();

        return [
            'data'              => $data,
            'draw'              => $draw,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $total,
            'dateRange'         => $dateRange,
        ];
    }

    public function setIds(?string $ids, array $idsParams, int $apiKey): void {
        $this->queryModel->setIds($ids, $idsParams, $apiKey);
    }

    protected function getData(): array {
        [$query, $params] = $this->queryModel->getData();

        $results = $this->execQuery($query, $params);

        $this->convertTimeToUserTimezone($results);
        $this->calculateCustomParams($results);

        return $results;
    }

    protected function getTotal(): int {
        [$query, $params] = $this->queryModel->getTotal();

        $results = $this->execQuery($query, $params);

        return $results[0]['count'];
    }

    protected function convertTimeToUserTimezone(array &$result): void {
        $result = sentrion('utils')->timezones->translateTimezones($result);
    }

    protected function calculateCustomParams(array &$result): void {
    }
}
