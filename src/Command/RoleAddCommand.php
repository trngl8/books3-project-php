<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RoleAddCommand extends Command
{
    protected static $defaultName = 'users:role';
    protected static $defaultDescription = 'Add role for user';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($this::$defaultName);
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('role', InputArgument::REQUIRED, 'Role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        if (!$username || !$role) {
            $io->error(sprintf('username and role mus be provided'));
            return Command::INVALID;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        $user->addRole($role);

        $this->em->flush();

        $io->success(sprintf('User have %d roles', count($user->getRoles())));

        return Command::SUCCESS;
    }
}