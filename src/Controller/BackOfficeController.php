<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ProductRepository $productRepository, Request $request, EntityManagerInterface $em): Response
    {
        // On récupère tous les produits de la base de données
        $products = $productRepository->findAll();

        // Formulaire pour ajouter un nouveau produit
        $newProduct = new Product();
        $formAdd = $this->createForm(ProductType::class, $newProduct);
        $formAdd->handleRequest($request);

        if ($formAdd->isSubmitted() && $formAdd->isValid()) {
            $em->persist($newProduct);
            $em->flush();
            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }

        // Gestion des modifications
        $formEdit = [];
        foreach ($products as $product) {
            $formEdit[$product->getId()] = $this->createForm(ProductType::class, $product);
            $formEdit[$product->getId()]->handleRequest($request);

            // Si le formulaire de modification est soumis et valide
            if ($formEdit[$product->getId()]->isSubmitted() && $formEdit[$product->getId()]->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Produit modifié avec succès.');
            }
        }

        // Suppression d'un produit
        if ($request->query->get('delete_id')) {
            $productToDelete = $productRepository->find($request->query->get('delete_id'));
            if ($productToDelete) {
                $em->remove($productToDelete);
                $em->flush();
                $this->addFlash('success', 'Produit supprimé avec succès.');
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'formAdd' => $formAdd->createView(),
            'formEdit' => $formEdit,
        ]);
    }
}
