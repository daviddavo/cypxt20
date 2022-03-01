<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    private function _userFromName($username)
    {
        $user = new User();
        $user->setUsername($username);
        $passwd = $this->hasher->hashPassword($user, $username);
        $user->setPassword($passwd);
        return $user;
    }

    public function load(ObjectManager $manager): void
    {
        $centralita = $this->_userFromName('centralita');
        $centralita->setRoles(['ROLE_CENTRALITA']);
        $manager->persist($centralita);
    
        $admin = $this->_userFromName('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
