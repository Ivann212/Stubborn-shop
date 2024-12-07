<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Créer un nouvel utilisateur
        $user = new User();

        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationType::class, $user);

        // Traiter le formulaire quand il est soumis
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            $encodedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            // Définir le rôle par défaut à ROLE_USER
            $user->setRole('ROLE_USER');

            // Sauvegarder l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger l'utilisateur après inscription (par exemple vers la page de connexion)
            return $this->redirectToRoute('app_login');
        }

        // Afficher le formulaire d'inscription
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
