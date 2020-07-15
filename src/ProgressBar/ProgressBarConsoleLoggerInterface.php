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

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Defines the interface for a logging to a {@see ProgressBar}.
 *
 * When implementing this interface it is advised to decorate an existing logger with this functionality to
 * also be able to actually log the messages to eg. a file.
 *
 * The context must allow control of the progression of the progress bar with the following keys:
 * - {@see ProgressBarConsoleLoggerInterface::PROGRESS_IDENTIFIER_CONTEXT_KEY}: The identifier of the progress bar
 * - {@see ProgressBarConsoleLoggerInterface::PROGRESS_INCREMENT_CONTEXT_KEY}: Increments the max steps of the progress bar
 * - {@see ProgressBarConsoleLoggerInterface::PROGRESS_ADVANCE_CONTEXT_KEY}: Advances the progress bar
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
interface ProgressBarConsoleLoggerInterface extends LoggerInterface
{
    /**
     * Logging context key to identify the registered {@see ProgressBar}.
     */
    public const PROGRESS_IDENTIFIER_CONTEXT_KEY = '_progress_logging_identifier';

    /**
     * Logging context key to increment the {@see ProgressBar}.
     */
    public const PROGRESS_INCREMENT_CONTEXT_KEY = '_progress_logging_increment';

    /**
     * Logging context key to advance the {@see ProgressBar}.
     */
    public const PROGRESS_ADVANCE_CONTEXT_KEY = '_progress_logging_advance';

    /**
     * Registers a {@see ProgressBar} to a specific identifier.
     *
     * The progress bar can be referenced by using the
     * {@see ProgressBarConsoleLoggerInterface::PROGRESS_IDENTIFIER_CONTEXT_KEY} key
     * in the {@see LoggerInterface::log} context.
     */
    public function registerProgressBar(string $identifier, ProgressBar $progressBar): void;
}
