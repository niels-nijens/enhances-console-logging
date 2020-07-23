<?php

declare(strict_types=1);

/*
 * This file is part of the EnhancesConsoleLogging package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\EnhancesConsoleLogging\ProgressBar;

use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Defines the constant to control of the progression of a progress bar
 * through a {@see ProgressBarConsoleLoggerInterface} implementation with the following keys:
 * - {@see LogContext::PROGRESS_IDENTIFIER_KEY}: The identifier of the progress bar
 * - {@see LogContext::PROGRESS_INCREMENT_KEY}: Increments the max steps of the progress bar
 * - {@see LogContext::PROGRESS_ADVANCE_KEY}: Advances the progress bar.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
interface LogContext
{
    /**
     * Logging context key to identify the registered {@see ProgressBar}.
     */
    public const PROGRESS_IDENTIFIER_KEY = '_progress_logging_identifier';

    /**
     * Logging context key to increment the {@see ProgressBar}.
     */
    public const PROGRESS_INCREMENT_KEY = '_progress_logging_increment';

    /**
     * Logging context key to advance the {@see ProgressBar}.
     */
    public const PROGRESS_ADVANCE_KEY = '_progress_logging_advance';
}
