<?php

namespace App\Command;

use App\Entity\Carousel;
use App\Entity\CarouselImage;
use App\Entity\Comic;
use App\Entity\HotBox;
use App\Entity\Image;
use App\Entity\User;
use App\Entity\Webring;
use App\Entity\WebringImage;
use App\Exceptions\RoleNotFoundException;
use App\Traits\CodeTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'hotbox:fixdefaults',
    description: 'Generates new codes for comics without them'
)]
final class FixComicCodesCommand extends Command
{
    use CodeTrait;

    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

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
     * @return int
     * @throws RoleNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('fix-defaults-command');

        $updateFields = [
            'comic' => [
                'entity' => Comic::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                    'code' => [
                        'get' => 'getCode',
                        'set' => 'setCode',
                        'value' => 'generateCode',
                    ]
                ],
            ],
            'carousels' => [
                'entity' => Carousel::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                    'code' => [
                        'get' => 'getCode',
                        'set' => 'setCode',
                        'value' => 'generateCode',
                    ],
                    'delay' => [
                        'get' => 'getDelay',
                        'set' => 'setDelay',
                        'value' => 30,
                    ]
                ],
            ],
            'carouselImages' => [
                'entity' => CarouselImage::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                ],
            ],
            'hotboxes' => [
                'entity' => HotBox::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                    'code' => [
                        'get' => 'getCode',
                        'set' => 'setCode',
                        'value' => 'generateCode',
                    ],
                ],
            ],
            'images' => [
                'entity' => Image::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                    'approved' => [
                        'get' => 'isApproved',
                        'set' => 'setApproved',
                        'value' => false,
                    ],
                ],
            ],
            'users' => [
                'entity' => User::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                    'deleted' => [
                        'get' => 'isDeleted',
                        'set' => 'setDeleted',
                        'value' => false,
                    ],
                ],
            ],
            'webrings' => [
                'entity' => Webring::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                    'code' => [
                        'get' => 'getCode',
                        'set' => 'setCode',
                        'value' => 'generateCode',
                    ],
                ],
            ],
            'webringImages' => [
                'entity' => WebringImage::class,
                'fields' => [
                    'active' => [
                        'get' => 'isActive',
                        'set' => 'setActive',
                        'value' => true,
                    ],
                ],
            ]
        ];

        $progress = new ProgressBar($output);

        foreach( $updateFields as $key => $item) {
            $data = $this->entityManager->getRepository($item['entity'])->findAll();
            $addItems = count($data) * count($item['fields']);
            $progress->setMaxSteps($progress->getMaxSteps() + $addItems);
            $fixed = [];
            foreach($data as $datum) {
                $persist = false;
                foreach ($item['fields'] as $field => $internal) {
                    $get = $internal['get'];
                    $set = $internal['set'];
                    $value = $internal['value'] === 'generateCode' ? $this->generateCode() : $internal['value'];

                    if ($datum->$get() === null) {
                        $datum->$set($value);
                        $class = $datum::class;
                        $id = $datum->getId();
                        $fixed[] = "{$class} : {$id} : {$field}";
                        $persist = true;
                    }
                    $progress->advance();
                }
                if ($persist) {
                    $this->entityManager->persist($datum);
                }
            }
        }
        $this->entityManager->flush();
        $progress->finish();
        $output->writeln("");
        $output->writeln("Update fields fix complete.");
        foreach ($fixed as $fix) {
            $output->writeln(" * {$fix}");
        }
        return Command::SUCCESS;
    }
}