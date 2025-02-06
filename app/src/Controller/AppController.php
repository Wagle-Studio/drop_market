<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    public const APP_HOME = "app_homepage";

    public function __construct(
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository
    ) {
    }

    #[Route("/", name: self::APP_HOME, methods: ["GET"])]
    public function index(Request $request): Response
    {
        // TODO: fix shop id is temporary.
        $shop = $this->shopRepository->find(1);
        $titleFilter = $request->query->get("title", "");

        $products = [];

        if ($shop && $shop->getId()) {
            $products = $this->productRepository->showcaseFilters(
                strval($shop->getId()),
                $titleFilter
            );
        }

        return $this->render("pages/app/homepage/homepage.html.twig", [
            "shop" => $shop,
            "products" => $products
        ]);
    }
}
