<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'hotbox:set-config',
    description: 'Updates the hostname setting in the database'
)]
class SetConfigCommand extends Command
{

    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('setting', InputArgument::OPTIONAL, 'Setting tag')
            ->addArgument('value', InputArgument::OPTIONAL, 'Setting value')
        ;
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('setting') && null !== $input->getArgument('value')) {
            return;
        }

        $this->io->title('Change Settings');

        
        $setting = $input->getArgument('setting');
        if (null !== $setting) {
            $this->io->text(' > <info>Setting Tag</info>: ' . $setting);
        } else {
            $setting = $this->io->ask('Setting Tag');
            $input->setArgument('setting', $setting);
        }

        $value = $input->getArgument('value');
        if (null !== $value) {
            $this->io->text(' > <info>Setting Value</info>: ' . $value);
        } else {
            $value = $this->io->ask('Setting Value');
            $input->setArgument('value', $value);
        }


    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('set-config-command');

        /** @var string $setting */
        $setting = $input->getArgument('setting');
        $value = $input->getArgument('value');

        /**
         * @var Settings $dbsetting
         */
        $dbsetting = $this->entityManager->getRepository(Settings::class)->findOneBy(['setting' => $setting]);
        if (empty($dbsetting)) {
            $this->io->error("Setting {$setting} not available");
            return Command::FAILURE;
        }
        $dbsetting->setValue($value);

        $this->entityManager->persist($dbsetting);
        $this->entityManager->flush();

        $this->io->success(sprintf('Config for %s has been updated to: %s', $setting, $value));

        $event = $stopwatch->stop('set-config-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('Elapsed time: %.2f ms / Consumed memory: %.2f MB', $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }

}