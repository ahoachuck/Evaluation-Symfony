<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findActiveProducts(),
            'metaTitle' => 'Accueil - Mini Boutique',
            'metaDescription' => 'Bienvenue sur notre mini boutique en ligne. Découvrez notre sélection de produits de qualité.'
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
        // Rediriger si le produit n'est pas actif et que l'utilisateur n'est pas admin
        if (!$product->isIsActive() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }
        
        // Récupérer les produits similaires (même catégorie)
        $similarProducts = [];
        if ($product->getCategory()) {
            $similarProducts = $productRepository->findSimilarProducts($product);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'similarProducts' => $similarProducts,
            'metaTitle' => $product->getName() . ' - Mini Boutique',
            'metaDescription' => substr($product->getDescription(), 0, 155) . '...'
        ]);
    }
}