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

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Allows logged messages to be displayed with a {@see ProgressBar}.
 *
 * The context allows control of the progression of the progress bar with the following keys:
 * - {@see ProgressBarConsoleLoggerInterface::PROGRESS_IDENTIFIER_CONTEXT_KEY}: The identifier of the progress bar
 * - {@see ProgressBarConsoleLoggerInterface::PROGRESS_INCREMENT_CONTEXT_KEY}: Increments the max steps of the progress bar
 * - {@see ProgressBarConsoleLoggerInterface::PROGRESS_ADVANCE_CONTEXT_KEY}: Advances the progress bar
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class ProgressBarConsoleLogger extends AbstractLogger implements ProgressBarConsoleLoggerInterface
{
    /**
     * @var ProgressBar[]
     */
    private $progressBars = [];

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * Constructs a new {@see ProgressBarConsoleLogger} instance.
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function registerProgressBar(string $identifier, ProgressBar $progressBar): void
    {
        $progressBar->setMessage('');

        $this->progressBars[$identifier] = $progressBar;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $this->logToProgressBar($level, $message, $context);
        $this->clearProgressLoggingFromContext($context);

        if ($this->logger === null) {
            return;
        }

        $this->logger->log($level, $message, $context);
    }

    /**
     * Logs the message to the {@see ProgressBar} provided in the context.
     */
    private function logToProgressBar(string $level, string $message, array $context): void
    {
        if (isset($context[self::PROGRESS_IDENTIFIER_CONTEXT_KEY]) === false) {
            return;
        }

        if (isset($this->progressBars[$context[self::PROGRESS_IDENTIFIER_CONTEXT_KEY]]) === false) {
            return;
        }

        $progressBar = $this->progressBars[$context[self::PROGRESS_IDENTIFIER_CONTEXT_KEY]];
        $progressBar->setMessage(
            sprintf(
                '<%s>%s</>',
                $level,
                $this->replaceMessagePlaceholdersWithContextData($message, $context)
            )
        );
        $progressBar->setMaxSteps($progressBar->getMaxSteps() + ($context[self::PROGRESS_INCREMENT_CONTEXT_KEY] ?? 0));
        $progressBar->advance($context[self::PROGRESS_ADVANCE_CONTEXT_KEY] ?? 0);
    }

    /**
     * Replaces placeholders in the message with data from the context.
     */
    private function replaceMessagePlaceholdersWithContextData(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            if (is_array($value) || (is_object($value) && method_exists($value, '__toString') === false)) {
                continue;
            }

            $replace['{'.$key.'}'] = strval($value);
        }

        return strtr($message, $replace);
    }

    /**
     * Clears the progress logging keys from the context, so this isn't passed along to another logger.
     */
    private function clearProgressLoggingFromContext(array &$context): void
    {
        unset($context[self::PROGRESS_IDENTIFIER_CONTEXT_KEY]);
        unset($context[self::PROGRESS_INCREMENT_CONTEXT_KEY]);
        unset($context[self::PROGRESS_ADVANCE_CONTEXT_KEY]);
    }
}
