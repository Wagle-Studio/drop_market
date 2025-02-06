<?php

namespace App\Controller;

use App\Entity\Wave;
use App\Entity\Shop;
use App\Entity\StatusWave;
use App\Form\WaveFormType;
use App\Security\Voters\ShopWaveVoter;
use App\Security\Voters\ShopVoter;
use App\Service\StatusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin/shops/{shop_slug}/creneaux")]
class AdminShopWaveController extends AbstractController
{
    public const SHOP_WAVE_COLLECTION = "admin_shop_wave_collection";
    public const SHOP_WAVE_READ = "admin_shop_wave_read";
    public const SHOP_WAVE_CREATE = "admin_shop_wave_create";
    public const SHOP_WAVE_EDIT = "admin_shop_wave_edit";
    public const SHOP_WAVE_DELETE = "admin_shop_wave_delete";

    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private StatusService $statusService,
    ) {
    }

    #[Route("", name: self::SHOP_WAVE_COLLECTION, methods: ["GET"])]
    public function index(#[ValueResolver("slug_resolver")] Shop $shop): Response
    {
        if (!$this->isGranted(ShopVoter::READ_SHOP, $shop)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render("pages/admin_shop_wave/collection/collection.html.twig", [
            "shop" => $shop,
        ]);
    }

    #[Route("/{wave_ulid}/details", name: self::SHOP_WAVE_READ, methods: ["GET"])]
    public function read(
        #[ValueResolver("slug_resolver")] Shop $shop,
        #[ValueResolver("ulid_resolver")] Wave $wave,
    ): Response {
        if (
            !$this->isGranted(ShopVoter::READ_SHOP, $shop) ||
            !$this->isGranted(ShopWaveVoter::READ_SHOP_WAVE, $wave)
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render("pages/admin_shop_wave/read/read.html.twig", [
            "shop" => $shop,
            "wave" => $wave,
        ]);
    }

    #[Route("/creation", name: self::SHOP_WAVE_CREATE, methods: ["GET", "POST"])]
    public function create(#[ValueResolver("slug_resolver")] Shop $shop, Request $request): Response
    {
        if (!$this->isGranted(ShopVoter::READ_SHOP, $shop)) {
            throw $this->createAccessDeniedException();
        }

        $wave = new Wave();
        $wave->setShop($shop);

        if (!$this->isGranted(ShopWaveVoter::CREATE_SHOP_WAVE, $wave)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(WaveFormType::class, $wave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $action = $request->request->get("action");

            $wave = $this->statusService->setStatus(
                $wave,
                StatusWave::class,
                $action === "draft" ? StatusWave::DRAFT : StatusWave::PUBLISHED
            );

            $this->entityManagerInterface->persist($wave);
            $this->entityManagerInterface->flush();

            $flashMessage = $action === "draft" ?
                "Créneau brouillon crée avec succès." :
                "Créneau créé et publié avec succès.";
            $this->addFlash("success", $flashMessage);

            return $this->redirectToRoute("admin_shop_wave_collection", ["shop_slug" => $shop->getSlug()]);
        }

        return $this->render("pages/admin_shop_wave/create/create.html.twig", [
            "shop" => $shop,
            "waveForm" => $form
        ]);
    }

    #[Route("/{wave_ulid}/edition", name: self::SHOP_WAVE_EDIT, methods: ["GET", "POST"])]
    public function edit(
        #[ValueResolver("slug_resolver")] Shop $shop,
        #[ValueResolver("ulid_resolver")] Wave $wave,
        Request $request
    ): Response {
        if (
            !$this->isGranted(ShopVoter::READ_SHOP, $shop) ||
            !$this->isGranted(ShopWaveVoter::CREATE_SHOP_WAVE, $wave)
        ) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(WaveFormType::class, $wave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $action = $request->request->get("action");

            if ($action === "publish" || $action === "unpublish") {
                $wave = $this->statusService->setStatus(
                    $wave,
                    StatusWave::class,
                    $action === "unpublish" ? StatusWave::DRAFT : StatusWave::PUBLISHED
                );
            }

            $this->entityManagerInterface->persist($wave);
            $this->entityManagerInterface->flush();

            switch ($action) {
                case "publish":
                    $flashMessage = "Créneau publié avec succès.";
                    break;
                case "unpublish":
                    $flashMessage = "Créneau dépublié avec succès.";
                    break;
                default:
                    $flashMessage = "Créneau mis à jour avec succès.";
            }

            $this->addFlash(
                "success",
                $flashMessage
            );

            return $this->redirectToRoute("admin_shop_wave_collection", ["shop_slug" => $shop->getSlug()]);
        }

        return $this->render("pages/admin_shop_wave/edit/edit.html.twig", [
            "shop" => $shop,
            "wave" => $wave,
            "waveForm" => $form
        ]);
    }

    #[Route("/{wave_ulid}/suppression", name: self::SHOP_WAVE_DELETE, methods: ["POST"])]
    public function delete(
        #[ValueResolver("slug_resolver")] Shop $shop,
        #[ValueResolver("ulid_resolver")] Wave $wave,
        Request $request
    ): Response {
        if (
            !$this->isGranted(ShopVoter::READ_SHOP, $shop) ||
            !$this->isGranted(ShopWaveVoter::DELETE_SHOP_WAVE, $wave)
        ) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid("delete_shop_wave" . $wave->getId(), $request->request->get("_token"))) {
            throw $this->createAccessDeniedException("Une erreur est survenue.");
        }

        $this->entityManagerInterface->remove($wave);
        $this->entityManagerInterface->flush();

        $this->addFlash("success", "Créneau supprimé avec succès.");

        return $this->redirectToRoute("admin_shop_wave_collection", ["shop_slug" => $shop->getSlug()]);
    }
}
