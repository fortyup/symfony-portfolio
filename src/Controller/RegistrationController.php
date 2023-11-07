<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données soumises par le formulaire
            $formData = $form->getData();

            // Vérifiez si les mots de passe correspondent
            if ($form->isSubmitted() && $form->isValid()) {
                // Vérifiez si les mots de passe correspondent en utilisant ExpressionLanguage
                $expressionLanguage = new ExpressionLanguage();
                $passwordMatches = $expressionLanguage->evaluate('this["plainPassword"].getData() == value', [
                    'this' => $form,
                    'value' => $form->get('plainPassword')->getData(),
                ]);

                if ($passwordMatches) {
                    // Les mots de passe correspondent, procédez à l'inscription
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );

                    $entityManager->persist($user);
                    $entityManager->flush();
                    // do anything else you need here, like send an email
                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                } else {
                    $this->addFlash('danger', 'The password and confirmation do not match.');
                }
            }
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
