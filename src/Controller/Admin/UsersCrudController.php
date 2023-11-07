<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {

    }

    public static function getEntityFqcn(): string
    {
        return Users::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            yield TextField::new('name'),
            yield TextField::new('firstname'),
            yield EmailField::new('email'),
            yield TextField::new('password'),
            yield ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->setChoices(
                    [
                        'Administrateur ' => 'ROLE_ADMIN',
                        'User' => 'ROLE_USER'
                    ]
                ),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManagerInterface, $entityInstance): void
    {
        /** @var Users $user */
        $user = $entityInstance;

        $plainPassword = $user->getPassword();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        parent::persistEntity($entityManagerInterface, $entityInstance);
    }
}
