<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ProductRepository $productRepository): Response
    {
        // Récupérer les produits "isFeatured" à partir du repository
        $featuredProducts = $productRepository->findFeaturedProducts();

        return $this->render('home/index.html.twig', [
            'featuredProducts' => $featuredProducts, // Passer les produits en vedette à la vue
        ]);
    }
}
