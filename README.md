# enhances-console-logging

Components to enhance Symfony console &amp; stdout logging.


## Installation using Composer

Run the following command to add the package to the composer.json of your project:

```bash
composer require niels-nijens/enhances-console-logging
```


## Usage

### ProgressBarConsoleLogger

The `ProgressBarConsoleLogger` allows you to log and show progress in a console without directly injecting a
`ProgressBar` into your domain components handling the progression. Instead, you are able to inject logging into those
components. This allows you to not only log to the console/stdout, but also to other logging mechanisms (eg. a file).

Logging to both a progress bar and another logging mechanism can be achieved by injecting/decorating an existing PSR-3
logger, like [Monolog](https://github.com/Seldaek/monolog).

The `ProgressBarConsoleLogger` adheres to the [PSR-3 logging specification](https://www.php-fig.org/psr/psr-3/) and as such implements the `Psr\Log\LoggerInterface`.

```php
<?php

declare(strict_types=1);

use Nijens\EnhancesConsoleLogging\ProgressBar\ProgressBarConsoleLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/** @var $monolog LoggerInterface */
/** @var $output OutputInterface */
$progressBar = new ProgressBar($output);

$logger = new ProgressBarConsoleLogger($monolog);
$logger->registerProgressBar(
    'test', // A unique identifier to reference the progress bar through the log context.
    $progressBar
);

$logger->info(
    'This message will be set on the progress bar.',
    [
        ProgressBarConsoleLogger::PROGRESS_IDENTIFIER_CONTEXT_KEY => 'test', // The unique identifier.
        ProgressBarConsoleLogger::PROGRESS_INCREMENT_CONTEXT_KEY => 0, // Allows incrementing the max steps. (optional)
        ProgressBarConsoleLogger::PROGRESS_ADVANCE_CONTEXT_KEY => 0, // Allows advancing the steps. (optional)
    ]
);

```

## Testing and code standards
Unit tests can be executed by running the following command:
```bash
composer test
```

Code style can be fixed or validated by running one of the following commands:
```bash
composer cs-fixer || composer cs-validate
```

## Security

If you discover any security related issues, please email
**nijens.niels+security [at] gmail.com** instead of using the issue tracker.


## Credits

* [Niels Nijens][link-author]
* [All Contributors][link-contributors]


## License
This package is licensed under the MIT License. Please see the [LICENSE file][link-license] for more information.


[link-author]: https://github.com/niels-nijens
[link-contributors]: https://github.com/niels-nijens/enhances-console-logging/graphs/contributors
[link-license]: LICENSE
