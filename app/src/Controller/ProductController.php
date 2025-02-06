<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/produits")]
class ProductController extends AbstractController
{
    public const PRODUCT_READ = "product_read";

    #[Route("/{product_slug}", name: self::PRODUCT_READ, methods: ["GET"])]
    public function read(#[ValueResolver("slug_resolver")] Product $product): Response
    {
        return $this->render("pages/product/read/read.html.twig", [
            "product" => $product
        ]);
    }
}
