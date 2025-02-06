<?php

namespace Tests\E2E\_utils;

use App\Entity\User;
use Symfony\Component\Panther\Client;

class ActionHelper
{
    use AppRouteTrait;

    public function __construct(private Client $client)
    {
    }

    public function visit(string $url): void
    {
        $this->client->request("GET", $url);
    }

    public function registerAs(string $email, string $firstname, string $lastname, string $password): void
    {
        $this->visit(self::AUTH_REGISTER_PATH);
        $this->fillAndSubmitFormType("register_form", "Inscription", [
            "email" => $email,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "plainPassword" => $password,
            "plainPasswordConfirmation" => $password,
            "agreeTerms" => "1",
        ]);
    }

    public function loginAs(string $role, string $email = "", string $password = ""): void
    {
        $requiredUser = match ($role) {
            User::ROLE_SUPER_ADMIN => ["super_admin@wgls.fr", "123456"],
            User::ROLE_ADMIN => ["admin@wgls.fr", "123456"],
            User::ROLE_OWNER => ["owner@wgls.fr", "123456"],
            User::ROLE_EMPLOYEE => ["employee@wgls.fr", "123456"],
            User::ROLE_USER => ["user@wgls.fr", "123456"],
            "invalid_user" => ["invalid_user@wgls.fr", "invalid_password"],
            "custom_user" => [$email, $password],
            default => [],
        };

        $this->visit(self::AUTH_LOGIN_PATH);
        $this->fillAndSubmitForm("Connexion", [
            "email" => $requiredUser[0],
            "password" => $requiredUser[1]
        ]);
    }

    /**
     * @param array<string, mixed> $formData
     */
    public function fillAndSubmitForm(string $button, array $formData): void
    {
        $crawler = $this->client->refreshCrawler();
        $form = $crawler->selectButton($button)->form();
        foreach ($formData as $key => $value) {
            $form[$key] = $value;
        }
        $this->client->submit($form);
    }

    /**
     * @param array<string, mixed> $formData
     */
    public function fillAndSubmitFormType(string $formName, string $button, array $formData): void
    {
        $crawler = $this->client->refreshCrawler();
        $form = $crawler->selectButton($button)->form();
        foreach ($formData as $key => $value) {
            $form[$formName . "[" . $key . "]"] = $value;
        }
        $this->client->submit($form);
    }
}
