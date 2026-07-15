<?php

/**
 * sentrion ~ open-source security framework
 * Copyright (c) Sentrion Technologies Sàrl (https://www.sentrion.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information    => please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Sentrion Technologies Sàrl (https://www.sentrion.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.sentrion.com Sentrion(tm)
 */

declare(strict_types=1);

$base = sentrion('storage')->get('BASE');
$errors = [];
$baseErrors = [
    'email_subject'         => 'Error %s occurred',
    'email_body_template'   => (
        '<p>Error occurred at: %s</p>
        <p>Host: %s</p>
        <p>Message: </p>%s
        <p>Trace: </p>%s
        '
    ),
    '404'                   => 'Page not found',
    '500'                   => 'This function does not work right now',
    sentrion('utils')->errorCodes->CSRF_ATTACK_DETECTED             => 'We can\'t proceed with this request. Please reload the page and try again',
    sentrion('utils')->errorCodes->EMAIL_DOES_NOT_EXIST             => 'Email does not exist',
    sentrion('utils')->errorCodes->EMAIL_IS_NOT_CORRECT             => 'Email is incorrect',
    sentrion('utils')->errorCodes->EMAIL_ALREADY_EXIST              => 'Email already exists',
    sentrion('utils')->errorCodes->PASSWORD_DOES_NOT_EXIST          => 'Password does not exist',
    sentrion('utils')->errorCodes->PASSWORD_IS_TOO_SHORT            => 'Minimum password length is 8 characters',
    sentrion('utils')->errorCodes->ACCOUNT_CREATED                  => 'Thanks for your registration. Please <a href="' . $base . '/login">login</a> with your new credentials.',
    sentrion('utils')->errorCodes->INSTALL_DIR_EXISTS               => 'Please delete /install folder before continue',

    sentrion('utils')->errorCodes->ACTIVATION_KEY_DOES_NOT_EXIST    => 'Activation key does not exist',
    sentrion('utils')->errorCodes->ACTIVATION_KEY_IS_NOT_CORRECT    => 'Activation key is incorrect',
    sentrion('utils')->errorCodes->EMAIL_OR_PASSWORD_IS_NOT_CORRECT => 'Error: Permission denied.',

    sentrion('utils')->errorCodes->API_KEY_ID_DOESNT_EXIST          => 'API key does not exist',
    sentrion('utils')->errorCodes->API_KEY_ID_INVALID               => 'Incorrect Tracking ID',
    sentrion('utils')->errorCodes->OPERATOR_ID_DOES_NOT_EXIST       => 'Operator ID does not exist',
    sentrion('utils')->errorCodes->OPERATOR_IS_NOT_A_CO_OWNER       => 'Operator is not a co-owner of this Tracking ID',
    sentrion('utils')->errorCodes->UNKNOWN_ENRICHMENT_ATTRIBUTES    => 'Unknown event attributes for data enrichment',
    sentrion('utils')->errorCodes->INVALID_API_RESPONSE             => 'Unexpected API response',

    sentrion('utils')->errorCodes->FIRST_NAME_DOES_NOT_EXIST        => 'First name is a mandatory field',
    sentrion('utils')->errorCodes->LAST_NAME_DOES_NOT_EXIST         => 'Last name is a mandatory field',
    sentrion('utils')->errorCodes->COUNTRY_DOES_NOT_EXIST           => 'Country is a mandatory field',
    sentrion('utils')->errorCodes->STREET_DOES_NOT_EXIST            => 'Street address is a mandatory field',
    sentrion('utils')->errorCodes->CITY_DOES_NOT_EXIST              => 'City is a mandatory field',
    sentrion('utils')->errorCodes->STATE_DOES_NOT_EXIST             => 'State is a mandatory field',
    sentrion('utils')->errorCodes->ZIP_DOES_NOT_EXIST               => 'ZIP is a mandatory field',
    sentrion('utils')->errorCodes->TIME_ZONE_DOES_NOT_EXIST         => 'Time zone is a mandatory field',
    sentrion('utils')->errorCodes->RETENTION_POLICY_DOES_NOT_EXIST  => 'Retention policy is a mandatory field',
    sentrion('utils')->errorCodes->INVALID_REMINDER_FREQUENCY       => 'Unreviewed items reminder frequency is a mandatory field',

    sentrion('utils')->errorCodes->CURRENT_PASSWORD_DOES_NOT_EXIST  => 'Current password is a mandatory field',
    sentrion('utils')->errorCodes->CURRENT_PASSWORD_IS_NOT_CORRECT  => 'Current password is incorrect',
    sentrion('utils')->errorCodes->NEW_PASSWORD_DOES_NOT_EXIST      => 'New password is a mandatory field',
    sentrion('utils')->errorCodes->PASSWORD_CONFIRMATION_MISSING    => 'Password confirmation is a mandatory field',
    sentrion('utils')->errorCodes->PASSWORDS_ARE_NOT_EQUAL          => 'New password and password confirmation do not match',
    sentrion('utils')->errorCodes->EMAIL_IS_NOT_NEW                 => 'The new email address is the same as the current one',

    sentrion('utils')->errorCodes->RENEW_KEY_CREATED                => 'We sent you an email with further instructions on how to reset your password',
    sentrion('utils')->errorCodes->RENEW_KEY_IS_NOT_CORRECT         => 'Renew key is incorrect  ¯\_ (ツ)_/¯',
    sentrion('utils')->errorCodes->RENEW_KEY_DOES_NOT_EXIST         => 'Renew key does not exist',
    sentrion('utils')->errorCodes->RENEW_KEY_WAS_EXPIRED            => 'Renew key has expired',
    sentrion('utils')->errorCodes->ACCOUNT_ACTIVATED                => 'Your password has been successfully changed. Please <a href="' . $base . '/login">login</a> with your new credentials and continue using the system.',

    sentrion('utils')->errorCodes->THERE_ARE_NO_EVENTS_YET          => 'No events from your application have been received yet',
    sentrion('utils')->errorCodes->THERE_ARE_NO_EVENTS_LAST_DAY     => 'There are no events from your application for more than 24 hours',
    sentrion('utils')->errorCodes->OPERATION_NOT_PERMITTED          => 'Operation is not permitted',

    sentrion('utils')->errorCodes->USER_ADDED_TO_REVIEW             => 'Entity has been successfully added to review queue',
    sentrion('utils')->errorCodes->USER_ADDED_TO_WATCHLIST          => 'Entity has been successfully added to the watchlist',
    sentrion('utils')->errorCodes->USER_REMOVED_FROM_WATCHLIST      => 'Entity has been successfully removed from the watchlist',
    sentrion('utils')->errorCodes->USER_FRAUD_FLAG_SET              => 'Entity has been successfully marked as fraud',
    sentrion('utils')->errorCodes->USER_FRAUD_FLAG_UNSET            => 'Entity has been successfully marked as not fraud',
    sentrion('utils')->errorCodes->USER_REVIEWED_FLAG_SET           => 'Entity has been successfully marked as reviewed',
    sentrion('utils')->errorCodes->USER_REVIEWED_FLAG_UNSET         => 'Entity has been successfully marked as not reviewed',
    sentrion('utils')->errorCodes->USER_DELETION_FAILED             => 'Entity deletion was unsuccessful.',
    sentrion('utils')->errorCodes->USER_BLACKLISTING_FAILED         => 'Entity blacklisting was unsuccessful.',
    sentrion('utils')->errorCodes->USER_BLACKLISTING_QUEUED         => 'This entity and all associated IPs are currently queued for blacklisting.',

    sentrion('utils')->errorCodes->CHANGE_EMAIL_KEY_DOES_NOT_EXIST  => 'Change email key does not exist',
    sentrion('utils')->errorCodes->CHANGE_EMAIL_KEY_IS_NOT_CORRECT  => 'Change email key is incorrect',
    sentrion('utils')->errorCodes->CHANGE_EMAIL_KEY_WAS_EXPIRED     => 'Change email key has expired',
    sentrion('utils')->errorCodes->EMAIL_CHANGED                    => 'Your email has been successfully changed. Please <a href="' . $base . '/login">login</a> with your new credentials and continue using the system.',
    sentrion('utils')->errorCodes->RULES_SUCCESSFULLY_UPDATED       => 'Rules have been successfully updated',
    sentrion('utils')->errorCodes->INVALID_BLACKLIST_THRESHOLD      => 'Blacklist threshold is a mandatory field.',
    sentrion('utils')->errorCodes->INVALID_REVIEW_QUEUE_THRESHOLD   => 'Review queue threshold is a mandatory field.',
    sentrion('utils')->errorCodes->INVALID_THRESHOLDS_COMBINATION   => 'Blacklist threshold must not exceed review queue threshold.',
    sentrion('utils')->errorCodes->INVALID_RULES_PRESET_ID          => 'Invalid rules preset ID.',

    sentrion('utils')->errorCodes->REST_API_KEY_DOES_NOT_EXIST      => 'API key could not be found in the headers',
    sentrion('utils')->errorCodes->REST_API_KEY_IS_NOT_CORRECT      => 'API key is incorrect',
    sentrion('utils')->errorCodes->REST_API_NOT_AUTHORIZED          => 'Not authorized to perform this action',
    sentrion('utils')->errorCodes->REST_API_MISSING_PARAMETER       => 'Missing required parameter',
    sentrion('utils')->errorCodes->REST_API_VALIDATION_ERROR        => 'Validation error',
    sentrion('utils')->errorCodes->REST_API_USER_ALREADY_DELETING   => 'Entity already scheduled for deletion',
    sentrion('utils')->errorCodes->REST_API_USER_ADDED_FOR_DELETION => 'Entity added to deletion queue',

    sentrion('utils')->errorCodes->ENRICHMENT_API_KEY_NOT_EXISTS    => 'Enrichment API key is not set',
    sentrion('utils')->errorCodes->TYPE_DOES_NOT_EXIST              => 'Type is a mandatory field',
    sentrion('utils')->errorCodes->SEARCH_QUERY_DOES_NOT_EXIST      => 'Search query is a mandatory field',
    sentrion('utils')->errorCodes->ENRICHMENT_API_UNKNOWN_ERROR     => 'Unknown error occurred while processing your request',
    sentrion('utils')->errorCodes->ENRICHMENT_API_BOGON_IP          => 'IP is bogon',
    sentrion('utils')->errorCodes->ENRICHMENT_API_IP_NOT_FOUND      => 'IP not found',
    sentrion('utils')->errorCodes->RISK_SCORE_UPDATE_UNKNOWN_ERROR  => 'Unknown error occurred while processing your request',
    sentrion('utils')->errorCodes->ENRICHMENT_API_KEY_OVERUSE       => 'You\'ve used up your Enrichment API quota. Please update your <a href="' . $base . '/api#subscription">plan</a>.',
    sentrion('utils')->errorCodes->ENRICHMENT_API_ATTR_UNAVAILABLE  => 'Enrichment of this data type is not supported in current subscription.',
    sentrion('utils')->errorCodes->ENRICHMENT_API_IS_NOT_AVAILABLE  => 'API server is currently unavailable. Please try again later.',

    sentrion('utils')->errorCodes->ITEM_REMOVED_FROM_BLACKLIST      => 'Item removed from blacklist.',
    sentrion('utils')->errorCodes->ITEM_REMOVE_FAIL_FROM_BLACKLIST  => 'Item remove from blacklist failed.',

    sentrion('utils')->errorCodes->SUBSCRIPTION_KEY_INVALID_UPDATE  => 'Enrichment key is not valid.',
    sentrion('utils')->errorCodes->TOTALS_INVALID_TYPE              => 'Invalid entity type was passed for totals calculation',
    sentrion('utils')->errorCodes->CRON_JOB_MAY_BE_OFF              => 'A cron job isn\'t running. Please check the cron job configuration.',
];

$baseErrors = (sentrion('storage')->get('EXTRA_DICT_EN_ERRORS') ?? []) + $baseErrors;
foreach ($baseErrors as $key => $value) {
    $errors['error_' . strval($key)] = $value;
}

return $errors;
