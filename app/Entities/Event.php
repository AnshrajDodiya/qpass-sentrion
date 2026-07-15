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

class Event {
    protected int $id;
    protected ?string $traceId;
    protected ?int $httpCode;
    protected ?int $httpMethodId;
    protected ?string $httpMethodValue;
    protected ?string $httpMethodName;
    protected ?int $typeId;
    protected ?string $typeValue;
    protected ?string $typeName;

    protected \Sentrion\Entities\User $user;
    protected \Sentrion\Entities\Email|\Sentrion\Entities\EmptyEmail $email;
    protected \Sentrion\Entities\Phone|\Sentrion\Entities\EmptyPhone $phone;
    protected \Sentrion\Entities\Device $device;
    protected \Sentrion\Entities\Ip $ip;
    protected \Sentrion\Entities\Session $session;
    protected \Sentrion\Entities\Resource $resource;
    protected \Sentrion\Entities\Query|\Sentrion\Entities\EmptyQuery $query;
    protected \Sentrion\Entities\Referer|\Sentrion\Entities\EmptyReferer $referer;
    protected \Sentrion\Entities\Payload $payload;

    protected string $time;

    protected int $key;

    protected array $nestedProps = ['user', 'email', 'phone', 'device', 'ip', 'session', 'resource', 'query', 'referer', 'payload'];
    protected array $tsFields = ['time'];

    public function __construct(
        int $id,
        ?string $traceId,
        ?int $httpCode,
        ?int $httpMethodId,
        ?string $httpMethodValue,
        ?string $httpMethodName,
        ?int $typeId,
        ?string $typeValue,
        ?string $typeName,
        \Sentrion\Entities\User $user,
        \Sentrion\Entities\Email|\Sentrion\Entities\EmptyEmail $email,
        \Sentrion\Entities\Phone|\Sentrion\Entities\EmptyPhone $phone,
        \Sentrion\Entities\Device $device,
        \Sentrion\Entities\Ip $ip,
        \Sentrion\Entities\Session $session,
        \Sentrion\Entities\Resource $resource,
        \Sentrion\Entities\Query|\Sentrion\Entities\EmptyQuery $query,
        \Sentrion\Entities\Referer|\Sentrion\Entities\EmptyReferer $referer,
        \Sentrion\Entities\Payload $payload,
        string $time,
        int $key,
    ) {
        $this->id               = $id;
        $this->traceId          = $traceId;
        $this->httpCode         = $httpCode;
        $this->httpMethodId     = $httpMethodId;
        $this->httpMethodValue  = $httpMethodValue;
        $this->httpMethodName   = $httpMethodName;
        $this->typeId           = $typeId;
        $this->typeValue        = $typeValue;
        $this->typeName         = $typeName;
        $this->user             = $user;
        $this->email            = $email;
        $this->phone            = $phone;
        $this->device           = $device;
        $this->ip               = $ip;
        $this->session          = $session;
        $this->resource         = $resource;
        $this->query            = $query;
        $this->referer          = $referer;
        $this->payload          = $payload;
        $this->time             = $time;
        $this->key              = $key;
    }

    public static function getById(int $id, int $key): ?self {
        $model = new \Sentrion\Models\Query\Events($key);

        return $model->where('event_id', '=', $id)->get()->data[0] ?? null;
    }

    public static function getFromQuery(array $data, int $key): self {
        return new self(
            $data['event_id'],
            $data['event_traceid'],
            $data['event_http_code'],
            $data['event_http_method_id'],
            $data['event_http_method_value'],
            $data['event_http_method_name'],
            $data['event_type_id'],
            $data['event_type_value'],
            $data['event_type_name'],
            sentrion('entities')->user->getFromQuery($data, $key),
            isset($data['email_id']) ? sentrion('entities')->email->getFromQuery($data, $key) : sentrion('entities')->emptyEmail->get(),
            isset($data['phone_id']) ? sentrion('entities')->phone->getFromQuery($data, $key) : sentrion('entities')->emptyPhone->get(),
            sentrion('entities')->device->getFromQuery($data, $key),
            sentrion('entities')->ip->getFromQuery($data, $key),
            sentrion('entities')->session->getFromQuery($data, $key),
            sentrion('entities')->resource->getFromQuery($data, $key),
            isset($data['url_query_id']) ? sentrion('entities')->query->getFromQuery($data, $key) : sentrion('entities')->emptyQuery->get(),
            isset($data['referer_id']) ? sentrion('entities')->referer->getFromQuery($data, $key) : sentrion('entities')->emptyReferer->get(),
            sentrion('entities')->payload->getFromQuery($data, $key),
            $data['event_time'],
            $key,
        );
    }
}
