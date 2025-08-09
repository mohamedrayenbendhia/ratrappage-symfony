<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-super-admin',
    description: 'Create a super admin user',
)]
class CreateSuperAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Supprimer l'utilisateur existant s'il existe
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'superadmin@admin.com']);
        if ($existingUser) {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
            $io->info('Existing super admin removed.');
        }

        // Créer le nouveau super admin
        $user = new User();
        $user->setEmail('superadmin@admin.com');
        $user->setName('Super Admin');
        $user->setPhoneNumber('12345678');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setIsBlocked(false);
        $user->setIsVerified(true);

        // Hacher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Super admin created successfully!');
        $io->table(['Field', 'Value'], [
            ['Email', 'superadmin@admin.com'],
            ['Password', 'password'],
            ['Role', 'ROLE_SUPER_ADMIN'],
            ['Status', 'Verified and Active']
        ]);

        return Command::SUCCESS;
    }
}
