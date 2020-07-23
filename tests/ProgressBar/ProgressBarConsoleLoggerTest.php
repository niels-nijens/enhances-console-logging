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

namespace Nijens\EnhancesConsoleLogging\Tests\ProgressBar;

use Nijens\EnhancesConsoleLogging\ProgressBar\LogContext;
use Nijens\EnhancesConsoleLogging\ProgressBar\ProgressBarConsoleLogger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Tests the {@see ProgressBarConsoleLogger}.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class ProgressBarConsoleLoggerTest extends TestCase
{
    /**
     * @var ProgressBarConsoleLogger
     */
    private $logger;

    /**
     * @var MockObject|LoggerInterface
     */
    private $decoratedLoggerMock;

    /**
     * Creates a new {@see ProgressBarConsoleLogger} for testing.
     */
    protected function setUp(): void
    {
        $this->decoratedLoggerMock = $this->createMock(LoggerInterface::class);

        $this->logger = new ProgressBarConsoleLogger($this->decoratedLoggerMock);
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::registerProgressBar} sets an empty message.
     */
    public function testSetsEmptyMessageUponRegistrationOfAProgressBar(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $this->logger->registerProgressBar('test', $progressBar);

        $this->assertSame('', $progressBar->getMessage());
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::log} sets the log message on the {@see ProgressBar}.
     */
    public function testLogsMessageToRegisteredProgressBar(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $this->logger->registerProgressBar('test', $progressBar);
        $this->logger->info(
            'Logging this message to the progress bar.',
            [LogContext::PROGRESS_IDENTIFIER_KEY => 'test']
        );

        $this->assertSame(
            '<info>Logging this message to the progress bar.</>',
            $progressBar->getMessage()
        );
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::log} sets the increments the max steps on the {@see ProgressBar}.
     */
    public function testIncrementsProgressBarThroughLogContext(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $this->logger->registerProgressBar('test', $progressBar);
        $this->logger->info(
            'Logging this message to the progress bar.',
            [
                LogContext::PROGRESS_IDENTIFIER_KEY => 'test',
                LogContext::PROGRESS_INCREMENT_KEY => 5,
            ]
        );

        $this->assertSame(
            5,
            $progressBar->getMaxSteps()
        );
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::log} advances the progress on the {@see ProgressBar}.
     */
    public function testAdvancesProgressBarThroughLogContext(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $this->logger->registerProgressBar('test', $progressBar);
        $this->logger->info(
            'Logging this message to the progress bar.',
            [
                LogContext::PROGRESS_IDENTIFIER_KEY => 'test',
                LogContext::PROGRESS_ADVANCE_KEY => 1,
            ]
        );

        $this->assertSame(
            1,
            $progressBar->getProgress()
        );
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::log} skips logging to the {@see ProgressBar}
     * when no matching identifier is found.
     */
    public function testSkipsProgressBarLoggingWhenNoMatchingIdentifierIsFound(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $this->logger->registerProgressBar('test', $progressBar);
        $this->logger->info(
            'Logging this message to the progress bar.',
            [
                LogContext::PROGRESS_IDENTIFIER_KEY => 'does-not-exist',
                LogContext::PROGRESS_ADVANCE_KEY => 1,
            ]
        );

        $this->assertEmpty($progressBar->getMessage());
        $this->assertSame(0, $progressBar->getProgress());
        $this->assertSame(0, $progressBar->getMaxSteps());
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::log} skips logging to the {@see ProgressBar}
     * when no progress logging context is available in the context.
     */
    public function testSkipsProgressBarLoggingNoProgressLoggingContextIsAvailableOnTheLogCall(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $this->logger->registerProgressBar('test', $progressBar);
        $this->logger->info(
            'Logging this message to the progress bar.',
            []
        );

        $this->assertEmpty($progressBar->getMessage());
        $this->assertSame(0, $progressBar->getProgress());
        $this->assertSame(0, $progressBar->getMaxSteps());
    }

    /**
     * Tests if {@see ProgressBarConsoleLogger::log} passes the log call to the decorated logger
     * without progress logging context.
     */
    public function testPassesLogToDecoratedLoggerWithoutProgressLoggingContext(): void
    {
        $this->decoratedLoggerMock->expects($this->once())
            ->method('log')
            ->with(LogLevel::INFO, 'Logging this message to the progress bar.', []);

        $this->logger->info(
            'Logging this message to the progress bar.',
            [
                LogContext::PROGRESS_IDENTIFIER_KEY => 'test',
                LogContext::PROGRESS_INCREMENT_KEY => 1,
                LogContext::PROGRESS_ADVANCE_KEY => 1,
            ]
        );
    }
}
