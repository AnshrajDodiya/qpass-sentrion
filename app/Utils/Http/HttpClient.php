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

namespace Sentrion\Utils\Http;

class HttpClient {
    private array $transports;

    public function __construct(array $transports) {
        $this->transports = $transports;
    }

    public static function default(): self {
        $transports = [
            \Sentrion\Utils\Http\CurlTransport::class,
            \Sentrion\Utils\Http\StreamTransport::class,
        ];

        return new self($transports);
    }

    public function request(\Sentrion\Entities\HttpRequest $request, ?int $apiKey = null): \Sentrion\Entities\HttpResponse {
        $response = null;

        $time = new \DateTime();
        $milliseconds = intval(intval($time->format('u')) / 1000);
        $time = $time->format('Y-m-d H:i:s') . '.' . sprintf('%03d', $milliseconds);

        foreach ($this->transports as $transport) {
            if ($transport::isAvailable()) {
                $response = $transport::request($request);
                break;
            }
        }

        $response = $response ?: sentrion('entities')->httpResponse->failure(null, 'no_transport_available', []);

        $this->saveLogbook($request, $response, $time, $apiKey);

        return $response;
    }

    private function saveLogbook(\Sentrion\Entities\HttpRequest $request, \Sentrion\Entities\HttpResponse $response, string $startTime, ?int $apiKey): void {
        sentrion('entities')->logbook->addRecord(
            $request->url,
            $startTime,                                     //$started,
            null,                                           //$ip,
            null,                                           //$eventId,
            $response->error,                               //$errorText,
            $response->body ? json_encode($response->body) : null,  //$raw,
            $apiKey,
            $response->ok ? sentrion('utils')->constants->LOGBOOK_ERROR_TYPE_SUCCESS : sentrion('utils')->constants->LOGBOOK_ERROR_TYPE_CRITICAL_ERROR,
            //$ended,
        );
    }
}
