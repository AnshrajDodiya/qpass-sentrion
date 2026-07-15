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

class Log {
    private function logObj(string $logFileVar = 'LOG_FILE'): \Log {
        return new \Log(sentrion('storage')->get($logFileVar));
    }

    public function log(?string $title, string|array $message): void {
        $logger = $this->logObj();

        if (is_array($message)) {
            $message = var_export($message, true);
        }

        if ($title) {
            $message = $this->logLine($title, $message);
        }

        $logger->write($message);
    }

    public function logSql(string $title, string $message): void {
        $logger = $this->logObj('LOG_SQL_FILE');
        $logDelim = sentrion('storage')->get('LOG_DELIMITER');
        $logger->write($this->logLine($title, $message, $logDelim));
    }

    public function logSqlIfPossible(): void {
        $printSqlToLog = sentrion('storage')->get('PRINT_SQL_LOG_AFTER_EACH_SCRIPT_CALL');
        if ($printSqlToLog) {
            $path = sentrion('request')->getPath();

            $log = sentrion('utils')->database->getDb()->log();
            if ($log) {
                $this->logSql($path, $log);
            }
        }
    }

    public function debug(string $msg, mixed ...$args): void {
        if (!sentrion('utils')->variables->getDebug()) {
            return;
        }

        $msg = $this->logLine('DEBUG', sprintf($msg, ...$args));
        $this->logObj()->write($msg);

        if (sentrion('utils')->variables->getLogToStderr()) {
            error_log($msg);
        }
    }

    public function info(string $msg, mixed ...$args): void {
        $msg = $this->logLine('INFO', sprintf($msg, ...$args));
        $this->logObj()->write($msg);

        if (sentrion('utils')->variables->getLogToStderr()) {
            error_log($msg);
        }
    }

    public function error(string $msg, mixed ...$args): void {
        $msg = $this->logLine('ERROR', sprintf($msg, ...$args));
        $this->logObj()->write($msg);

        error_log($msg);
    }

    public function logbookRequest(
        string $endpoint,
        ?string $started,
        ?string $ip,
        ?int $eventId,
        ?string $errorText,
        ?string $raw,
        int $apiKey,
        int $errorType = 0,
        ?string $ended = null,
    ): void {
        sentrion('entities')->logbook->addRecord($endpoint, $started, $ip, $eventId, $errorText, $raw, $apiKey, $errorType, $ended);
    }

    private function logLine(string $title, string $message, string $delim = ''): string {
        return '[' . getmypid() . '] ' . $title . ': ' . $message . $delim;
    }
}
