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

class Request {
    private ?array $payload = null;
    private array $timers = [];

    public function getRequestType(): ?string {
        return sentrion('storage')->get('VERB');
    }

    public function isCli(): bool {
        return sentrion('storage')->get('CLI');
    }

    public function isAjax(): bool {
        return sentrion('storage')->get('AJAX');
    }

    public function isPost(): bool {
        return $this->getRequestType() === 'POST';
    }

    public function isGet(): bool {
        return $this->getRequestType() === 'GET';
    }

    public function isPut(): bool {
        return $this->getRequestType() === 'PUT';
    }

    public function isDelete(): bool {
        return $this->getRequestType() === 'DELETE';
    }

    public function getRouteAlias(): ?string {
        return sentrion('storage')->get('ALIAS') ?: null;
    }

    public function getUserAgent(): string {
        return sentrion('storage')->get('AGENT');
    }

    public function getXFrame(): ?string {
        return sentrion('storage')->get('XFRAME') ?: null;
    }

    public function getUri(): string {
        return sentrion('storage')->get('URI');
    }

    public function getQuery(): ?string {
        return sentrion('storage')->get('QUERY') ?: null;
    }

    public function isHttps(): bool {
        return sentrion('storage')->get('SCHEME') === 'https';
    }

    public function getPattern(): string {
        return sentrion('storage')->get('PATTERN');
    }

    public function getPath(): string {
        return sentrion('storage')->get('PATH');
    }

    public function getGet(): ?array {
        return sentrion('storage')->get('GET');
    }

    public function getBody(): array|string|null {
        return sentrion('storage')->get('BODY');
    }

    public function getPost(): ?array {
        return sentrion('storage')->get('POST');
    }

    public function getUrlParams(): array {
        return sentrion('storage')->get('PARAMS');
    }

    public function getUrlParam(string $key): ?string {
        return sentrion('storage')->get('PARAMS.' . $key);
    }

    public function getStringUrlParam(string $key, bool $nullable = true): ?string {
        $param = $this->getUrlParam($key);

        return $param !== null ? strval($param) : ($nullable ? null : '');
    }

    public function getIntUrlParam(string $key, bool $nullable = true): ?int {
        $param = $this->getUrlParam($key);

        return $param !== null ? sentrion('utils')->conversion->intVal($param) : ($nullable ? null : 0);
    }

    public function getIp(): ?string {
        return sentrion('storage')->get('IP') ?: null;
    }

    public function getHeaders(): array {
        return sentrion('storage')->get('HEADERS');
    }

    public function getHeader(string $key): ?string {
        return sentrion('storage')->get('HEADERS.' . $key);
    }

    public function getContentType(): ?string {
        return $this->getHeader('Content-Type');
    }

    public function contentTypeIsJson(): bool {
        return str_contains($this->getContentType() ?? '', 'application/json');
    }

    public function contentTypeIsUrlEncoded(): bool {
        return str_contains($this->getContentType() ?? '', 'x-www-form-urlencoded');
    }

    public function getFragment(): ?string {
        return sentrion('storage')->get('FRAGMENT') ?: null;
    }

    public function getAllPayload(): array {
        if ($this->payload === null) {
            $get =  $this->getGet() ?? [];
            $post = $this->getPost() ?? [];
            $body = [];
            if ($this->contentTypeIsJson()) {
                $body = json_decode($this->getBody() ?? '[]', true) ?? [];
            } elseif ($this->contentTypeIsUrlEncoded()) {
                parse_str($this->getBody() ?? '', $body);
            }

            $payload = $get + $post + $body;
            $this->payload = $payload;
        }

        return $this->payload;
    }

    public function getRequestParam(string $key): mixed {
        return $this->getAllPayload()[$key] ?? null;
    }

    public function getStringRequestParam(string $key, bool $nullable = true): ?string {
        $param = $this->getRequestParam($key);

        return $param !== null ? strval($param) : ($nullable ? null : '');
    }

    public function getIntRequestParam(string $key, bool $nullable = true): ?int {
        $param = $this->getRequestParam($key);

        return $param !== null ? sentrion('utils')->conversion->intVal($param) : ($nullable ? null : 0);
    }

    public function getArrayRequestParam(string $key, bool $nullable = true): ?array {
        $param = $this->getRequestParam($key);

        return is_array($param) ? array_values($param) : ($nullable ? null : []);
    }

    public function getDictionaryRequestParam(string $key, bool $nullable = true): ?array {
        $param = $this->getRequestParam($key);

        return is_array($param) ? $param : ($nullable ? null : []);
    }

    public function validateCsrf(): int|false {
        $csrf = sentrion('session')->get('csrf');
        $token = $this->getAllPayload()['token'] ?? null;

        if (!$csrf || !$token || $token !== $csrf) {
            return sentrion('utils')->errorCodes->CSRF_ATTACK_DETECTED;
        }

        return false;
    }

    public function registerRoute(string $method, string $route, callable|string $call): void {
        sentrion('router')->route($method . ' ' . $route, $call);
    }

    public function setTimer(): int {
        $time = microtime(true);
        $idx = count($this->timers);

        $this->timers[] = $time;

        return $idx;
    }

    public function getTimer(int $idx = 0): ?float {
        $time = $this->timers[$idx] ?? null;

        return $time !== null ? microtime(true) - $time : null;
    }

    public function resetPayloadCache(): void {
        $this->payload = null;
    }
}
