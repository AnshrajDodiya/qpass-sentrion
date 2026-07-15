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

// NOTE: has nested entities
class Ip extends \Sentrion\Entities\Single {
    protected int $id;

    protected string $ip;
    protected ?string $cidr;
    protected ?bool $dataCenter;
    protected ?bool $tor;
    protected ?bool $vpn;
    protected ?bool $starlink;
    protected ?bool $blocklist;
    protected ?bool $relay;
    protected ?bool $checked;
    protected ?int $shared;
    protected ?bool $fraud;

    protected \Sentrion\Entities\Isp $isp;
    protected \Sentrion\Entities\Country $country;

    protected ?int $totalVisit;

    protected string $lastseen;
    protected string $created;
    protected ?string $updated;

    protected int $key;

    protected array $nestedProps = ['isp', 'country'];
    protected array $tsFields = ['created', 'lastseen', 'updated'];

    public function __construct(
        int $id,
        string $ip,
        ?string $cidr,
        ?bool $dataCenter,
        ?bool $tor,
        ?bool $vpn,
        ?bool $starlink,
        ?bool $blocklist,
        ?bool $relay,
        ?bool $checked,
        ?int $shared,
        ?bool $fraud,
        \Sentrion\Entities\Isp $isp,
        \Sentrion\Entities\Country $country,
        ?int $totalVisit,
        string $lastseen,
        string $created,
        ?string $updated,
        int $key,
    ) {
        $this->id           = $id;
        $this->ip           = $ip;
        $this->cidr         = $cidr;
        $this->dataCenter   = $dataCenter;
        $this->tor          = $tor;
        $this->vpn          = $vpn;
        $this->starlink     = $starlink;
        $this->blocklist    = $blocklist;
        $this->relay        = $relay;
        $this->checked      = $checked;
        $this->shared       = $shared;
        $this->fraud        = $fraud;
        $this->isp          = $isp;
        $this->country      = $country;
        $this->totalVisit   = $totalVisit;
        $this->lastseen     = $lastseen;
        $this->created      = $created;
        $this->updated      = $updated;
        $this->key          = $key;
    }

    public static function getById(int $id, int $key): ?self {
        $model = new \Sentrion\Models\Query\Ips($key);

        return $model->where('ip_id', '=', $id)->get()->data[0] ?? null;
    }

    public static function getFromQuery(array $data, int $key): self {
        return new self(
            $data['ip_id'],
            $data['ip_ip'],
            $data['ip_cidr'],
            $data['ip_data_center'],
            $data['ip_tor'],
            $data['ip_vpn'],
            $data['ip_starlink'],
            $data['ip_blocklist'],
            $data['ip_relay'],
            $data['ip_checked'],
            $data['ip_shared'],
            $data['ip_fraud_detected'],
            sentrion('entities')->isp->getFromQuery($data, $key),
            sentrion('entities')->country->getFromQuery($data, $key),
            $data['ip_total_visit'],
            $data['ip_lastseen'],
            $data['ip_created'],
            $data['ip_updated'],
            $key,
        );
    }
}
