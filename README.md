# Correlation Ids Monolog Processor Library

> PHP library for correlation ids monolog processor based on the [correlation ids library](https://github.com/oat-sa/lib-correlation-ids).

## Table of contents
- [Installation](#installation)
- [Principles](#principles)
- [Usage](#usage)
- [Tests](#tests)

## Installation

```console
$ composer require oat-sa/lib-correlation-ids-monolog
```

## Principles

This library provides a ready to use [monolog](https://github.com/Seldaek/monolog) processor that will extend the logs context with the correlation ids fetched from the [correlation ids registry](https://github.com/oat-sa/lib-correlation-ids/blob/master/src/Registry/CorrelationIdsRegistryInterface.php).

## Usage

You need to push the `CorrelationIdsMonologProcessor` to your logger as follow:

```php
<?php declare(strict_types=1);

use OAT\Library\CorrelationIds\Registry\CorrelationIdsRegistry;
use OAT\Library\CorrelationIds\Registry\CorrelationIdsRegistryInterface;
use OAT\Library\CorrelationIdsMonolog\Processor\CorrelationIdsMonologProcessor;
use Monolog\Logger;

/** @var CorrelationIdsRegistryInterface $registry */
$registry = new CorrelationIdsRegistry(...);

$logger = new Logger('channel-name');
$logger->pushProcessor(new CorrelationIdsMonologProcessor($registry));

...

$logger->info('log message'); // this log entry context will be automatically populated with the correlation ids.
```
**Note**: you can customize the log context key names by providing you own [CorrelationIdsHeaderNamesProviderInterface](https://github.com/oat-sa/lib-correlation-ids/blob/master/src/Provider/CorrelationIdsHeaderNamesProviderInterface.php) implementation and pass it to the `CorrelationIdsMonologProcessor` constructor.

## Tests

To run tests:
```console
$ vendor/bin/phpunit
```
**Note**: see [phpunit.xml.dist](phpunit.xml.dist) for available test suites.