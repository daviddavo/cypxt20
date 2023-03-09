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
    name: 'cypxt:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['cypxt:add-user']
)]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'cypxt:create-user';

    private UserRepository $userManager;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userManager, UserPasswordHasherInterface $hasher)
    {
        $this->userManager = $userManager;
        $this->hasher = $hasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp("This command allows you to create a user")
            ->addArgument('username', InputArgument::REQUIRED, "User name")
            ->addArgument('password', InputArgument::REQUIRED, "User Password")
            ->addArgument('roles', InputArgument::IS_ARRAY, "Roles to add");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ... put here the code to create the user
        $output->writeln('Creating user');

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        $roles[] = 'ROLE_USER';

        $output->writeln('Username: '.$username);
        $output->writeln('Roles: '.implode(", ", $roles));

        $user = new User();
        $user->setUsername($username);
        $password = $this->hasher->hashPassword($user, $password);
        $user->setPassword($password);
        $user->setRoles($roles);

        $this->userManager->add($user);

        return Command::SUCCESS;
    }
}