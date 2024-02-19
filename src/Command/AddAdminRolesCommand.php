<?php

namespace App\Command;

use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use App\Exceptions\HotBoxException;
use App\Exceptions\RoleNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use function Symfony\Component\String\u;

#[AsCommand(
    name: 'add:admin',
    description: 'Creates a new administrator'
)]
class AddAdminRolesCommand extends Command
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
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new admin')
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
        $this->io->title('Add admin roles to a User');


        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email');
            $input->setArgument('email', $email);
        }

    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws RoleNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-roles-command');

        /** @var string $email */
        $email = $input->getArgument('email');

        $roleList = [RoleEnumeration::ROLE_ADMIN, RoleEnumeration::ROLE_CREATOR];


        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (empty($user)) {
            throw new HotBoxException("User does not exist");
        }
        // create the user and hash its password
        $user->setRoles($roleList);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('Roles were successfully created: %s', $user->getEmail()));

        $event = $stopwatch->stop('add-role-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }
}