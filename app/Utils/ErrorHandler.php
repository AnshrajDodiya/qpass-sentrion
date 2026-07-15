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

class ErrorHandler {
    public static function getErrorDetails(): array {
        $errorTraceArray = [];

        $errorTraceString = sentrion('storage')->get('ERROR.trace');
        $errorTraceArray = preg_split('/$\R?^/m', $errorTraceString);
        $maximalStringIndex = 0;
        $maximalStringLength = 0;
        $iters = count($errorTraceArray);

        for ($i = 0; $i < $iters; ++$i) {
            $currentStringLength = strlen($errorTraceArray[$i]);
            if ($maximalStringLength < $currentStringLength) {
                $maximalStringIndex = $i;
                $maximalStringLength = $currentStringLength;
            }
        }

        if ($iters > 1) {
            array_splice($errorTraceArray, $maximalStringIndex, 1);
        }

        $iters = count($errorTraceArray);
        for ($i = 0; $i < $iters; ++$i) {
            $errorTraceArray[$i] = strip_tags($errorTraceArray[$i]);
            $errorTraceArray[$i] = str_replace(['&gt;', '&lt;'], ['>', '<'], $errorTraceArray[$i]);
        }

        $errorCode = sentrion('storage')->get('ERROR.code');
        $errorMessage = join(', ', ['ERROR_' . $errorCode, sentrion('storage')->get('ERROR.text')]);

        return [
            'ip'        => sentrion('request')->getIp(),
            'code'      => $errorCode,
            'message'   => $errorMessage,
            'trace'     => join('<br>', $errorTraceArray),
            'date'      => date('l jS \of F Y h:i:s A'),
            'post'      => sentrion('storage')->get('POST'),
            'get'       => sentrion('storage')->get('GET'),
        ];
    }

    public static function saveErrorInformation(array $errorData): void {
        sentrion('utils')->logger->log(null, $errorData['message']);

        $errorTraceArray = explode('<br>', $errorData['trace']);
        $printErrorTraceToLog = sentrion('storage')->get('PRINT_ERROR_TRACE_TO_LOG');
        if ($printErrorTraceToLog) {
            $iters = count($errorTraceArray);

            for ($i = 0; $i < $iters; ++$i) {
                sentrion('utils')->logger->log(null, $errorTraceArray[$i]);
            }
        }

        $database = sentrion('utils')->database->getDb();
        if ($database && sentrion('utils')->routes->getCurrentRequestOperator()->isLoggedIn()) {
            $errorData['sql_log'] = $database->log();
            sentrion('models')->log->insertRecord($errorData);

            sentrion('utils')->logger->log('SQL', $errorData['sql_log']);
        }

        if ($errorData['code'] === 500) {
            $toName = 'Admin';
            $toAddress = sentrion('utils')->variables->getAdminEmail();
            if ($toAddress === null) {
                sentrion('utils')->logger->log('Log mail error', 'ADMIN_EMAIL is not set');

                return;
            }

            $subject = sentrion('storage')->get('error_email_subject') ?? sentrion('utils')->constants->BASE_ERROR_EMAIL_SUBJECT;
            $subject = sprintf($subject, $errorData['code']);

            $currentTime = date('d-m-Y H:i:s');
            $errorMessage = $errorData['message'];
            $errorTrace = $errorData['trace'];

            $hosts = json_encode(sentrion('utils')->variables->getHosts());

            $message = sentrion('storage')->get('error_email_body_template') ?? sentrion('utils')->constants->BASE_ERROR_EMAIL_BODY_TEMPLATE;
            $message = sprintf($message, $currentTime, $hosts, $errorMessage, $errorTrace);

            sentrion('utils')->mailer->send($toName, $toAddress, $subject, $message, true);
        }
    }

    protected static function getAjaxErrorMessage(array $errorData): string|false {
        return json_encode(
            [
                'status' => false,
                'code' => $errorData['code'],
                'message' => sprintf('Request finished with code %s', $errorData['code']),
            ],
        );
    }

    public static function getOnErrorHandler(): callable {
        /**
         * Custom onError handler: http://stackoverflow.com/questions/19763414/fat-free-framework-f3-custom-404-page-and-others-errors, https://groups.google.com/forum/#!topic/f3-framework/BOIrLs5_aEA
         * We can can use $f3->get('ERROR.text'), and decide which template should be displayed.
         *
         */
        return function (): void {
            $errorData = self::getErrorDetails();

            // clean template if anything was rendered already
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            self::saveErrorInformation($errorData);

            if ($errorData['code'] === 403 && !sentrion('request')->isAjax()) {
                sentrion('response')->redirect('/logout');

                return;
            }

            // Add handling 404 error
            if ($errorData['code'] === 404) {
            }

            if (sentrion('request')->isAjax()) {
                echo self::getAjaxErrorMessage($errorData);

                return;
            }

            $errorData['message'] = 'ERROR_' . $errorData['code'];
            $errorData['raw'] = false;

            if ($errorData['code'] !== 404) {
                $errorData['extra_message'] = sentrion('storage')->get('ErrorPage_extra_message');
                $errorData['raw'] = true;
            }

            if ($errorData['code'] === 400) {
                $errorData['message'] = 'Error code ' . sentrion('utils')->errorCodes->INVALID_HOSTNAME;
                $errorData['extra_message'] = 'Visit page via correct hostname: ' . sentrion('utils')->variables->getHostWithProtocol() . sentrion('request')->getPath();
            }

            if ($errorData['code'] === 503) {
                $errorData['message'] = 'Error code ' . sentrion('utils')->errorCodes->FAILED_DB_CONNECT;
                $errorData['extra_message'] = 'Database connection failed.';
            }

            if ($errorData['code'] === 422) {
                $errorData['message'] = 'Error code ' . sentrion('utils')->errorCodes->INCOMPLETE_CONFIG;
                $errorData['extra_message'] = 'App configuration is incomplete. Check config/local/config.local.ini and possible environment overrides.';
            }

            if ($errorData['code'] === 500 && sentrion('utils')->variables->getDebugLevel() > 0) {
                $errorText = sentrion('storage')->get('ERROR.text');
                if ($errorText) {
                    $errorData['extra_message'] = strval($errorText);
                    $errorData['raw'] = false;
                }
            } else {
                unset($errorData['trace']);
            }

            $pageParams = sentrion('pages')->error->getPageParams($errorData);
            $response = new \Sentrion\Views\Frontend();

            $response->data = $pageParams;
            echo $response->render();
        };
    }

    public static function getCronErrorHandler(): callable {
        return function (): void {
            $errorData = self::getErrorDetails();
            self::saveErrorInformation($errorData);
        };
    }

    public static function exceptionErrorHandler(int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
}
