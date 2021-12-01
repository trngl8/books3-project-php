<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UsersListCommand extends Command
{
    protected static $defaultName = 'users:list';
    protected static $defaultDescription = 'List users';

    private $repository;

    public function __construct(UserRepository $repository)
    {
        parent::__construct($this::$defaultName);
        $this->repository = $repository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //TODO: get with limit
        $users = $this->repository->findAll();

        if(count($users) === 0) {
            $io->warning(sprintf('No users found.'));
            $io->writeln([
                'Next: Try to add users via command users:add username password',
            ]);

            return Command::SUCCESS;
        }

        $io->success(sprintf('Found %d users', count($users)));

        $tableHeaders = [
            'id', 'username', 'roles', 'verified', 'password', 'salt'
        ];

        $tableRows = array_map(function($item) {
            return [
                $item->getId(),
                $item->getUserIdentifier(),
                implode(',', $item->getRoles()),
                $item->isVerified(),
                $item->getPassword(),
                $item->getSalt(), //maybe remove candidate
            ];
        }, $users);

        $io->table($tableHeaders, $tableRows);

        return Command::SUCCESS;
    }
}