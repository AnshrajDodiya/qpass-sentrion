# sentrion

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/ec30c28f67de476f8b98d2798079bdf0)](https://app.codacy.com/gh/SentrionTechnologies/sentrion/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Docker Pulls](https://img.shields.io/docker/pulls/sentrion/sentrion?style=flat)](https://hub.docker.com/r/sentrion/sentrion/)

<p align="center">
    <a href="https://www.sentrion.com/" target="_blank">
        <img src="https://www.sentrion.com/firstscreen.jpg" alt="sentrion screenshot" />
    </a>
</p>

[sentrion](https://www.sentrion.com) is a security framework.

sentrion *[tir.ˈrɛ.no]* helps understand, monitor, and protect your product from threats, fraud, and abuse. While classic cybersecurity focuses on infrastructure and network perimeter, most breaches occur through compromised accounts and application logic abuse that bypasses firewalls, SIEM, WAFs, and other defenses. sentrion detects threats where they actually happen: inside your product.

sentrion is a hand-written, few-dependency, "low-tech" PHP/PostgreSQL application. After a straightforward five-minute installation, you can ingest events through API calls and immediately access a real-time threat dashboard.

## Core components
* **SDKs & API** Integrate sentrion into any product with SDKs.
  Send events with full context in a few lines of code.
* **Built-in dashboard** Monitor and understand your product's
  security events from a single interface. Ready for use in minutes.
* **Single user view** Analyze behaviour patterns, risk scores,
  connected identities, and activity timelines for a specific user.
* **Rule engine** Calculate risk scores automatically with preset
  rules or create your own customized for your product.
* **Review queue** Automatically suspend accounts with risky events
  or flag them for manual review through threshold settings.
* **Field audit trail** Track modifications to important fields,
  including what changed and when to streamline audit and compliance.

## Preset rules

`Account takeover` `Credential stuffing` `Content spam` `Account registration` `Fraud prevention` `Insider threat`
`Bot detection` `Dormant account` `Multi-accounting` `Promo abuse` `API protection` `High-risk regions`

## Built for

* **Self-hosted, internal and legacy apps**: Embed security layer
  to extend your security through audit trails, protect user accounts
  from takeover, detect cyber threats and monitor insider threats.
* **SaaS and digital platforms**: Prevent cross-tenant data leakage,
  online fraud, privilege escalation, data exfiltration and business
  logic abuse.
* **E-commerce and online marketplaces**: Detect payment fraud, account
  abuse, fake reviews, promotional code exploitation, inventory manipulation,
  and protect against credential stuffing and carding attacks.
* **Mission critical applications**: Sensitive application protection,
  even in air-gapped deployments.
* **Industrial control systems (ICS) and command & control (C2)**: Protect,
  operational technology, command systems, and critical infrastructure
  platforms from unauthorized access and malicious commands.
* **Non-human identities (NHIs)**: Monitor service accounts, API keys,
  bot behaviors, and detect compromised machine identities.
* **API-first applications**: Protect against abuse, rate limiting
  bypasses, scraping, and unauthorized access.

## Live demo

Check out the live demo at [play.sentrion.com](https://play.sentrion.com) (*admin/sentrion*).

## Requirements

* **PHP**: Version 8.0 to 8.3
* **PostgreSQL**: Version 12 or greater
* **PHP extensions**: `PDO_PGSQL`, `cURL`
* **HTTP web server**: `Apache` with `mod_rewrite` and `mod_headers` enabled
* **Operating system**: A Unix-like system is recommended
* **Minimum hardware requirements**:
  * **PostgreSQL**: 512 MB RAM (4 GB recommended)
  * **Application**: 128 MB RAM (1 GB recommended)
  * **Storage**: Approximately 3 GB PostgreSQL storage per 1 million events

## Docker-based installation

To run sentrion within a Docker container you may use command below:

```bash
curl -sL sentrion.com/t.yml | docker compose -f - up -d
```
Continue with step 4 of [Quickstart](#quickstart-install).

## Quickstart install
1. [Download](https://www.sentrion.com/download/) the latest version of sentrion (ZIP file).
2. Extract the sentrion-master.zip file to the location where you want it installed on your web server.
3. Navigate to `http://localhost:8585/install/index.php` in a browser to launch the installation process.
4. After the successful installation, delete the `install/` directory and its contents.
5. Navigate to `http://localhost:8585/signup/` in a browser to create an administrator account.
6. For cron job setup, insert the following schedule (every 10 minutes) expression with the `crontab -e` command or by editing the `/var/spool/cron/your-web-server` file:

```
*/10 * * * * /usr/bin/php /absolute/path/to/sentrion/index.php /cron
```

## Using Heroku (optional)

Click [here](https://heroku.com/deploy?template=https://github.com/sentriontechnologies/sentrion) to launch heroku deployment.

## Via Composer and Packagist (optional)

sentrion is published at Packagist and could be installed with Composer:

```
composer create-project sentrion/sentrion
```

or could be pulled into an existing project:

```
composer require sentrion/sentrion
```

## SDKs

* [PHP](https://github.com/sentriontechnologies/sentrion-php-tracker)
* [Python](https://github.com/sentriontechnologies/sentrion-python-tracker)
* [NodeJS](https://github.com/sentriontechnologies/sentrion-nodejs-tracker)
* [WordPress](https://github.com/sentriontechnologies/sentrion-wordpress-tracker)

## Custom page examples

sentrion ingests the universal primitives (users/entities, IPs, devices, sessions, events) and exposes them through composable machinery: the rule engine, the sentrion('queries') builder, and the file-based assets/pages/ extension system — through which an operator can express whatever risk model they have. The examples below show custom pages built on that extension system.

### LLM bots

* [llm-bots.example.php](https://github.com/sentriontechnologies/sentrion/blob/master/assets/pages/llm-bots.example.php)
* [llm-bots.example.html](https://github.com/sentriontechnologies/sentrion/blob/master/assets/pages/views/llm-bots.example.html)

### Risk users

* [risk-users.example.php](https://github.com/sentriontechnologies/sentrion/blob/master/assets/pages/risk-users.example.php)
* [risk-users.example.html](https://github.com/sentriontechnologies/sentrion/blob/master/assets/pages/views/risk-users.example.html)

## Documentation

See the [User guide](https://docs.sentrion.com/) for details on how to use sentrion, [Developers documentation](https://github.com/sentriontechnologies/DEVELOPMENT.md) to customize your integration, [Admin documentation](https://github.com/sentriontechnologies/ADMIN.md) for installation, maintenance and updates.

## About

sentrion is is a free, [open source security framework](https://www.sentrion.com). Event tracking, threat detection, and risk scoring for any product.

The project started as a proprietary system in 2021 and was open-sourced (AGPL) in December 2024.

Behind sentrion is a blend of extraordinary engineers and professionals, with over a decade of experience in cyberdefence. We solve real people's challenges through love in *ascétique* code and open technologies. sentrion is not VC-motivated. Our inspiration comes from the daily threats posed by organized cybercriminals, driving us to reimagine the place of security in modern applications.

## Why the name sentrion?

Tyrrhenian people may have lived in Tuscany and eastern Switzerland as far back as 800 BC. The term "Tyrrhenian" became more commonly associated with the Etruscans, and it is from them that the Tyrrhenian Sea derives its name, which is still in use today.

According to historical sources, Tyrrhenian people were the first to use trumpets for signaling about coming threats, which was later adopted by Greek and Roman military forces.

While working on the logo, we conducted our own historical study and traced mentions of 'sentrion' back to the 15th-century printed edition of the Vulgate (the Latin Bible). We kept it lowercase to stay true to the original — quite literally, by the book. The sentrion wordmark stands behind the horizon line, as a metaphor of the endless evolutionary cycle of the threat landscape and our commitment to rise over it.

## Links

* [Website](https://www.sentrion.com)
* [Live demo](https://play.sentrion.com)
* [Admin documentation](https://github.com/sentriontechnologies/ADMIN.md)
* [Developers documentation](https://github.com/sentriontechnologies/DEVELOPMENT.md)
* [Resource center](https://www.sentrion.com/bat/)
* [Docker Hub](https://hub.docker.com/r/sentrion/sentrion)
* [User guide](https://docs.sentrion.com)
* [Packagist](https://packagist.org/packages/sentrion/sentrion)
* [Mattermost community](https://chat.sentrion.com)

## Reporting a security issue

If you've found a security-related issue with sentrion, please email security@sentrion.com. Submitting the issue on GitHub exposes the vulnerability to the public, making it easy to exploit. We will publicly disclose the security issue after it has been resolved.

After receiving a report, sentrion will take the following steps:

* Confirm that the report has been received and is being addressed.
* Attempt to reproduce the problem and confirm the vulnerability.
* Release new versions of all the affected packages.
* Announce the problem prominently in the release notes.
* If requested, give credit to the reporter.

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License (AGPL) as published by the Free Software Foundation version 3.

The name "sentrion" is a registered trademark of sentrion technologies sàrl, and sentrion technologies sàrl hereby declines to grant a trademark license to "sentrion" pursuant to the GNU Affero General Public License version 3 Section 7(e), without a separate agreement with sentrion technologies sàrl.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License along with this program. If not, see [GNU Affero General Public License v3](https://www.gnu.org/licenses/agpl-3.0.txt).

## Authors

sentrion Copyright (C) 2026 sentrion technologies sàrl, Vaud, Switzerland. (License AGPLv3)

't'
