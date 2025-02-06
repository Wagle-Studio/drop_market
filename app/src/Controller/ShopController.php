<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\OrderFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/{shop_slug}")]
class ShopController extends AbstractController
{
    public const SHOP_CART = "shop_cart";

    #[Route("/panier", name: self::SHOP_CART, methods: ["GET", "POST"])]
    public function read(
        #[ValueResolver("slug_resolver")] Shop $shop,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order, [
            "shop" => $shop
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var User|null $orderAuthor */
            $orderAuthor = $this->getUser();

            if (!$orderAuthor) {
                throw $this->createAccessDeniedException();
            }

            $order->setUser($orderAuthor);

            $entityManagerInterface->persist($order);
            $entityManagerInterface->flush();
        }

        return $this->render("pages/shop/cart/cart.html.twig", [
            "shop" => $shop,
            "orderForm" => $form,
        ]);
    }
}
