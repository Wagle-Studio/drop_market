<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Form\ShopFormType;
use App\Security\Voters\ShopVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin/shops")]
class AdminShopController extends AbstractController
{
    public const SHOP_READ = "admin_shop_read";
    public const SHOP_EDIT = "admin_shop_edit";
    public const SHOP_DELETE = "admin_shop_delete";

    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }

    #[Route("/{shop_slug}", name: self::SHOP_READ, methods: ["GET"])]
    public function read(#[ValueResolver("slug_resolver")] Shop $shop): Response
    {
        if (!$this->isGranted(ShopVoter::READ_SHOP, $shop)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render("pages/admin_shop/read/read.html.twig", [
            "shop" => $shop
        ]);
    }

    #[Route("/{shop_slug}/edition", name: self::SHOP_EDIT, methods: ["GET", "POST"])]
    public function edit(#[ValueResolver("slug_resolver")] Shop $shop, Request $request): Response
    {
        if (!$this->isGranted(ShopVoter::EDIT_SHOP, $shop)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ShopFormType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManagerInterface->persist($shop);
            $this->entityManagerInterface->flush();

            $this->addFlash(
                "success",
                "Boutique mise à jour avec succès."
            );

            return $this->redirectToRoute("admin_shop_read", ["shop_slug" => $shop->getSlug()]);
        }

        return $this->render("pages/admin_shop/edit/edit.html.twig", [
            "shop" => $shop,
            "shopForm" => $form
        ]);
    }

    #[Route("/{shop_slug}/suppression", name: self::SHOP_DELETE, methods: ["POST"])]
    public function delete(
        #[ValueResolver("slug_resolver")] Shop $shop,
        Request $request,
    ): Response {
        if (!$this->isGranted(ShopVoter::DELETE_SHOP, $shop)) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid("delete_shop_" . $shop->getId(), $request->request->get("_token"))) {
            throw $this->createAccessDeniedException("Une erreur est survenue.");
        }

        $this->entityManagerInterface->remove($shop);
        $this->entityManagerInterface->flush();

        $this->addFlash("success", "Boutique supprimée avec succès.");

        return $this->redirectToRoute("profile_read");
    }
}
