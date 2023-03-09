<?php
namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'cypxt:delete-user',
    description: 'Deletes an existing user.',
    hidden: false,
    aliases: ['cypxt:rm-user']
)]
class DeleteUserCommand extends Command
{
    protected static $defaultName = 'cypxt:delete-user';

    private UserRepository $userManager;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userManager)
    {
        $this->userManager = $userManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp("This command allows you to delete a user")
            ->addArgument('username', InputArgument::REQUIRED, "User name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');

        $output->writeln('Deleting user ' . $username);

        $user = $this->userManager->findByUsername($username);

        if ($user != null) {
            $output->writeln("Deleting user with id " . $user->getId());
            $this->userManager->remove($user);
        } else {
            $output->writeln("User not found");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}