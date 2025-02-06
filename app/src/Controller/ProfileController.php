<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/profil")]
class ProfileController extends AbstractController
{
    public const PROFILE_READ = "profile_read";
    public const PROFILE_EDIT = "profile_edit";

    #[Route("", name: self::PROFILE_READ, methods: ["GET", "POST"])]
    public function read(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod("POST") && $request->headers->get("Turbo-frame")) {
            $turboFrameId = $request->headers->get("Turbo-frame");

            if ($turboFrameId === "stream_card_profile") {
                $form = $this->createForm(UserFormType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManagerInterface->persist($user);
                    $entityManagerInterface->flush();

                    return $this->render("components/organisms/card_profile_read/card_profile_read.html.twig", [
                        "user" => $user,
                    ]);
                }

                return $this->render("components/organisms/card_profile_edit/card_profile_edit.html.twig", [
                    "userForm" => $form->createView(),
                ]);
            }
        }

        return $this->render("pages/profile/read/read.html.twig", [
            "user" => $user,
            "shops" => $user->getShops()
        ]);
    }

    #[Route("/edition", name: self::PROFILE_EDIT, methods: ["GET"])]
    public function edit(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);

        return $this->render("components/organisms/card_profile_edit/card_profile_edit.html.twig", [
            "user" => $user,
            "shops" => $user->getShops(),
            "userForm" => $form
        ]);
    }
}
