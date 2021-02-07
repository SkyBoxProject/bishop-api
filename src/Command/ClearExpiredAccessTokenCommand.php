<?php

declare(strict_types=1);

namespace App\Command;

use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CronJob("0 0 * * *")
 */
final class ClearExpiredAccessTokenCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('cron:clear-expired-access-token')
            ->setDescription('Clear expired access token.');
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        return;
    }
}
