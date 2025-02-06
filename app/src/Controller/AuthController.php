<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordChangeFormType;
use App\Form\RegisterFormType;
use App\Form\PasswordResetFormType;
use App\Security\UserAuthenticator;
use App\Service\Contract\EmailInterface;
use App\Service\Contract\SecurityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route("/auth")]
class AuthController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public const AUTH_REGISTER = "auth_register";
    public const AUTH_LOGIN = "auth_login";
    public const AUTH_LOGOUT = "auth_logout";
    public const AUTH_EMAIL_VERIFY = "auth_email_verify";
    public const AUTH_PASSWORD_RESET = "auth_password_reset";
    public const AUTH_PASSWORD_CHANGE = "auth_password_change";

    public function __construct(
        private EmailInterface $emailInterface,
        private SecurityInterface $securityInterface,
        private EntityManagerInterface $entityManagerInterface,
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private ResetPasswordHelperInterface $resetPasswordHelperInterface
    ) {
    }

    #[Route("/inscription", name: self::AUTH_REGISTER, methods: ["GET", "POST"])]
    public function register(Request $request, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get("plainPassword")->getData();
            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, $plainPassword));

            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();

            $emailConfirmationSignatureContext = $this->securityInterface
                ->buildEmailConfirmationTemplateContext($user);
            $this->emailInterface->sendTemplatedMail(
                $user->getEmail(),
                "Confirmez votre adresse email",
                "emails/register_confirmation.html.twig",
                $emailConfirmationSignatureContext
            );

            $this->addFlash(
                "success",
                "Un email vous a été envoyé pour confirmer votre adresse email."
            );
            return $security->login($user, UserAuthenticator::class, "main");
        }

        return $this->render("pages/auth/register/register.html.twig", [
            "registrationForm" => $form,
        ]);
    }

    #[Route(path: "/connexion", name: self::AUTH_LOGIN, methods: ["GET", "POST"])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render("pages/auth/login/login.html.twig", [
            "last_username" => $authenticationUtils->getLastUsername(),
            "error" => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route(path: "/deconnexion", name: self::AUTH_LOGOUT, methods: ["GET"])]
    public function logout(): void
    {
    }

    #[Route("/email/verification", name: self::AUTH_EMAIL_VERIFY, methods: ["GET"])]
    public function userEmailVerify(Request $request): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        try {
            /** @var User $user */
            $user = $this->getUser();

            $this->securityInterface->verifyEmailRequestSignature($request, $user);
            $user->setVerified(true);

            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash(
                "error",
                "Nous avons rencontré un problème lors de la vérification de votre adresse e-mail."
            );
            return $this->redirectToRoute("app_homepage");
        }

        $this->addFlash("success", "Votre adresse e-mail a été vérifiée.");
        return $this->redirectToRoute("app_homepage");
    }

    #[Route("/mot-de-passe/reset", name: self::AUTH_PASSWORD_RESET, methods: ["GET", "POST"])]
    public function passwordReset(Request $request): Response
    {
        $form = $this->createForm(PasswordResetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->entityManagerInterface->getRepository(User::class)->findOneBy([
                "email" => $form->get("email")->getData(),
            ]);

            $this->addFlash(
                "success",
                "Un email vous a été envoyé pour changer votre mot de passe."
            );

            if (!$user) {
                return $this->redirectToRoute("app_homepage");
            }

            try {
                $resetToken = $this->resetPasswordHelperInterface->generateResetToken($user);
            } catch (ResetPasswordExceptionInterface $e) {
                return $this->redirectToRoute("app_homepage");
            }

            $this->emailInterface->sendTemplatedMail(
                $user->getEmail(),
                "Changement de mot de passe",
                "emails/password_reset.html.twig",
                ["resetToken" => $resetToken]
            );

            $this->setTokenObjectInSession($resetToken);

            return $this->redirectToRoute("app_homepage");
        }

        return $this->render("pages/auth/password_reset/password_reset.html.twig", [
            "resetPasswordForm" => $form,
        ]);
    }

    #[Route("/mot-de-passe/{token}", name: self::AUTH_PASSWORD_CHANGE, methods: ["GET", "POST"])]
    public function passwordChange(Request $request, ?string $token = null): Response
    {
        if ($token) {
            // Stores the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute("auth_password_change");
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException();
        }

        try {
            $user = $this->resetPasswordHelperInterface->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash(
                "error",
                "Nous avons rencontré un problème lors du changement de votre mot de passe."
            );
            return $this->redirectToRoute("auth_password_reset");
        }

        // The token is valid, allow the user to change their password.
        $form = $this->createForm(PasswordChangeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelperInterface->removeResetRequest($token);

            $plainPassword = $form->get("plainPassword")->getData();

            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, $plainPassword));
            $this->entityManagerInterface->flush();

            $this->cleanSessionAfterReset();

            $this->addFlash(
                "success",
                "Mot de passe mis à jour avec succès, veuillez vous connecter."
            );

            return $this->redirectToRoute("app_homepage");
        }

        return $this->render("pages/auth/password_change/password_change.html.twig", [
            "changePasswordForm" => $form,
        ]);
    }
}
