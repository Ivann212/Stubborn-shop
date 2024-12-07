<?php

namespace App\Controller;

use App\Form\LoginType; // Si vous avez créé un formulaire personnalisé (optionnel)
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, Security $security): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Créer un formulaire de connexion via Symfony
        $form = $this->createForm(LoginType::class); // Utiliser un formulaire de type LoginType
        $form->handleRequest($request); // Traiter la requête

        

        // Si le formulaire est soumis et valide, effectuer la connexion
        if ($form->isSubmitted() && $form->isValid()) {
            // Vous pouvez éventuellement ajouter une logique de redirection ici
            return $this->redirectToRoute('app_home');
        }

        // Renvoyer le formulaire à la vue
        return $this->render('security/login.html.twig', [
            'form' => $form->createView(), // Passer la vue du formulaire à la vue
        ]);
    }
}

