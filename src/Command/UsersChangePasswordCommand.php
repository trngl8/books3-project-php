<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersChangePasswordCommand extends Command
{
    protected static $defaultName = 'users:pass';
    protected static $defaultDescription = 'Change password for user';

    private $em;

    private $passwordEncoder;

    public function __construct(string $name = null, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        if (!$username || !$password) {
            $io->error('username and password mus be provided');
            return Command::INVALID;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $this->em->flush();

        $io->success('User password saved');

        return Command::SUCCESS;
    }
}