<?php
namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:set-user-password',
    description: 'Sets an user password.',
    hidden: false,
    aliases: ['app:set-password']
)]
class SetUserPasswordCommand extends Command
{
    protected static $defaultName = 'app:set-user-password';

    private UserRepository $userManager;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userManager, UserPasswordHasherInterface $hasher, ManagerRegistry $doctrine)
    {
        $this->userManager = $userManager;
        $this->hasher = $hasher;
        $this->entityManager = $doctrine->getManager();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp("This command allows you to create a user")
            ->addArgument('username', InputArgument::REQUIRED, "User name")
            ->addArgument('password', InputArgument::REQUIRED, "User Password");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $output->writeln('Seting user password for '. $username);

        $user = $this->userManager->findByUsername($username);

        if ($user == null) {
            $output->writeln("User not found");
            return Command::FAILURE;
        }

        $output->writeln("Changing password for user with id " . $user->getId());
        $password = $this->hasher->hashPassword($user, $password);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}