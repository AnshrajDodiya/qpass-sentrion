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

session_name('CONSOLESESSION');

ini_set('session.cookie_httponly', '1');

chdir(dirname(__FILE__));

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    require __DIR__ . '/libs/bcosca/fatfree-core/base.php';

    // PSR-4 autoloader
    spl_autoload_register(function (string $className): void {
        $libs = [
            'Ruler\\' => '/libs/ruler/ruler/src/',
            'PHPMailer\\PHPMailer\\' => '/libs/phpmailer/phpmailer/src/',
            'Sentrion\\' => '/app/',
        ];

        foreach ($libs as $namespace => $path) {
            if (str_starts_with($className, $namespace)) {
                require __DIR__ . $path . str_replace([$namespace, '\\'], ['', '/'], $className) . '.php';
                break;
            }
        }
    });
}

include './app/sentrion.php';

//Load configuration file with all project variables
sentrion('router')->config('config/config.ini');

//Load specific configuration only for local development
$localConfigFile = sentrion('utils')->variables->getConfigFile();
$localConfigFile = sprintf('config/%s', $localConfigFile);

//Load local configuration file
if (file_exists($localConfigFile)) {
    sentrion('router')->config($localConfigFile);
}

//Use custom onError function
sentrion('storage')->set('ONERROR', sentrion('utils')->errorHandler->getOnErrorHandler());

if (sentrion('utils')->variables->getForceHttps() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
    ini_set('session.cookie_secure', '1');
}

if (!sentrion('utils')->variables->completedConfig()) {
    if (is_file('./install/index.php')) {
        if ((sentrion('request')->getPath() === '/' || sentrion('request')->getPath() === '/index.php')) {
            sentrion('response')->redirect('./install/index.php');
        } else {
            header('HTTP/1.1 404 Page Not Found');
            echo 'Error ' . sentrion('utils')->errorCodes->INCOMPLETE_CONFIG . ' Configuration is missing. Please visit /install/ to continue.';
            exit(0);
        }
    } else {
        header('HTTP/1.1 404 Page Not Found');
        echo 'Error ' . sentrion('utils')->errorCodes->INCOMPLETE_CONFIG . ' Configuration and install/index.php are missing.';
        exit(0);
    }
}

//Load routes configuration
sentrion('router')->config('config/routes.ini');
sentrion('router')->config('config/apiEndpoints.ini');

//Override F3 host
sentrion('utils')->access->cleanHost();

if (sentrion('utils')->variables->getDB()) {
    //Load dictionary file
    sentrion('storage')->set('LOCALES', 'app/Dictionary/');
    sentrion('storage')->set('LANGUAGE', 'en');

    // tmp load all assets pages
    $pages = sentrion('assets')->pages->getAllPagesObjects();
}

sentrion('router')->run();
