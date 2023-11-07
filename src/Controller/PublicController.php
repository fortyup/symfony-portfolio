<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'app_public')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données soumises par le formulaire
            $data = $form->getData();

            // Associez les données à l'entité Contact
            $contact->setFirstName($data->getFirstName());
            $contact->setName($data->getName());
            $contact->setEmail($data->getEmail());
            $contact->setSubject($data->getSubject());
            $contact->setMessage($data->getMessage());

            // Persistez l'entité en base de données
            $entityManager->persist($contact);
            $entityManager->flush();

            // Ajoutez un message flash pour indiquer le succès de l'envoi
            $this->addFlash('success', 'Your message has been sent successfully!');

            // Redirigez vers une page de confirmation ou toute autre page souhaitée
            return $this->render('contact/confirmation.html.twig');
        }

        return $this->render('public/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
