<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductSize;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile; // Import nécessaire pour UploadedFile

class BackOfficeController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ProductRepository $productRepository, Request $request, EntityManagerInterface $em): Response
    {
        // Récupère tous les produits de la base de données
        $products = $productRepository->findAll();

        // Formulaire pour ajouter un nouveau produit
        $newProduct = new Product();
        $formAdd = $this->createForm(ProductType::class, $newProduct);
        $formAdd->handleRequest($request);

        if ($formAdd->isSubmitted() && $formAdd->isValid()) {
            // Gestion de l'image (si un fichier a été téléchargé)
            /** @var UploadedFile $imageFile */
            $imageFile = $formAdd->get('image')->getData();
            if ($imageFile) {
                // Créer un nom unique pour l'image
                $newFileName = uniqid().'.'.$imageFile->guessExtension();
                // Déplacer l'image dans le répertoire des images
                $imageFile->move(
                    $this->getParameter('images_directory'),  // Paramètre de configuration pour le répertoire
                    $newFileName
                );
                // Enregistrer le chemin du fichier dans la base de données
                $newProduct->setImage($newFileName);
            }

            // Persister les tailles et stocks
            foreach ($newProduct->getSizes() as $size) {
                $em->persist($size);  // Persister les tailles avec leur stock
            }

            // Persister le produit
            $em->persist($newProduct);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }

        // Gestion des modifications pour chaque produit
        $formEdit = [];
        foreach ($products as $product) {
            $formEdit[$product->getId()] = $this->createForm(ProductType::class, $product);
            $formEdit[$product->getId()]->handleRequest($request);

            // Si le formulaire de modification est soumis et valide
            if ($formEdit[$product->getId()]->isSubmitted() && $formEdit[$product->getId()]->isValid()) {
                // Gestion de l'image (si un fichier a été téléchargé)
                /** @var UploadedFile $imageFile */
                $imageFile = $formEdit[$product->getId()]->get('image')->getData();
                if ($imageFile) {
                    $newFileName = uniqid().'.'.$imageFile->guessExtension();
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFileName
                    );
                    $product->setImage($newFileName);
                }

                // Persister les modifications des tailles et stocks
                foreach ($product->getSizes() as $size) {
                    $em->persist($size);
                }

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

        return $this->render('back_office/index.html.twig', [
            'products' => $products,
            'formAdd' => $formAdd->createView(),
            'formEdit' => $formEdit,
        ]);
    }
}
