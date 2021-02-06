<?php

namespace Tests\Traits;

trait FileSystemTrait
{
    protected function getDataFixturesFolder(): string
    {
        return __DIR__.'/../DataFixtures/';
    }

    protected function loadLastEmailMessage(?string $mailDir = null): string
    {
        $mailDir = $mailDir ?: $this->getLogDirectory().'/test/mail/';
        $mailDir = rtrim($mailDir, DIRECTORY_SEPARATOR);

        $files = array_merge(glob($mailDir.DIRECTORY_SEPARATOR.'*.txt'));
        $lastFile = end($files);

        return file_get_contents($lastFile);
    }

    protected function getTemporaryDirectory(): string
    {
        $temporaryDirectory = __DIR__.'/../../var/tmp/';
        $this->createIfNotExistsDirectory($temporaryDirectory);

        return $temporaryDirectory;
    }

    protected function getLogDirectory(): string
    {
        $logDirectory = __DIR__.'/../../var/log/';
        $this->createIfNotExistsDirectory($logDirectory);

        return $logDirectory;
    }

    private function createIfNotExistsDirectory(string $directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
