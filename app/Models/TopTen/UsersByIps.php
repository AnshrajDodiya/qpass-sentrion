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

namespace Sentrion\Models\TopTen;

class UsersByIps extends Base {
    public function getList(int $apiKey, ?array $dateRange): array {
        $params = $this->getQueryParams($apiKey, $dateRange);

        $queryConditions = $this->getQueryConditions($dateRange);
        $queryConditions = join(' AND ', $queryConditions);

        $query = (
            "SELECT
                event_account.id            AS accountid,
                event_account.userid        AS accounttitle,
                event_account.fraud,
                event_account.score,
                event_account.score_updated_at,
                event_account.added_to_review,
                COUNT(DISTINCT event.ip)    AS value,
                event_email.email

            FROM
                event

            INNER JOIN event_account
            ON (event.account = event_account.id)

            LEFT JOIN event_email
            ON (event_account.lastemail = event_email.id)

            WHERE
                {$queryConditions}

            GROUP BY
                event_account.id,
                event_account.userid,
                event_email.email

            HAVING
                COUNT(DISTINCT event.ip) > 1

            ORDER BY
                value DESC

            LIMIT 10 OFFSET 0"
        );

        $results = $this->execQuery($query, $params);

        foreach ($results as &$row) {
            $tsColumns = ['score_updated_at'];
            $row = sentrion('utils')->timezones->localizeTimestampsForActiveOperator($tsColumns, $row);
        }

        unset($row);

        return $results;
    }
}
