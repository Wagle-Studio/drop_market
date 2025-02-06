<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\Stream\StreamCookieGenerator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: "onLoginSuccess")]
class AuthenticationSuccessEventListener
{
    private StreamCookieGenerator $cookieGenerator;

    public function __construct(StreamCookieGenerator $cookieGenerator)
    {
        $this->cookieGenerator = $cookieGenerator;
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $mercureCookie = $this->cookieGenerator->generate($user);

        $response = $event->getResponse();
        $response->headers->set('set-cookie', $mercureCookie, false);

        $event->setResponse($response);
    }
}
