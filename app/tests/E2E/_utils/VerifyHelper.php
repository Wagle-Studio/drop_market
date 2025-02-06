<?php

namespace Tests\E2E\_utils;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\Client;

class VerifyHelper
{
    public function __construct(
        private Client $client,
        private WebTestCase $webTestCase
    ) {
    }

    public function currentUrlPathMatch(string $expectedPath): void
    {
        $currentPath = parse_url($this->client->getCurrentURL(), PHP_URL_PATH);
        $this->webTestCase->assertSame($expectedPath, $currentPath);
    }

    /**
     * @param array<string, string> $cookieNames
     */
    public function sessionCookieExists(array $cookieNames): void
    {
        $this->webTestCase->assertContains("MOCKSESSID", $cookieNames);
    }


    /**
     * @param array<string, string> $cookieNames
     */
    public function sessionCookieDoesNotExists(array $cookieNames): void
    {
        $this->webTestCase->assertNotContains("MOCKSESSID", $cookieNames);
    }

    public function formExists(string $expectedFormName): void
    {
        $this->webTestCase->assertSelectorExists("form[name='{$expectedFormName}']");
    }

    public function pageElementExists(string $expectedNeedle): void
    {
        $this->webTestCase->assertStringContainsString(
            $expectedNeedle,
            $this->client->getPageSource()
        );
    }

    public function entityExist(mixed $entity): void
    {
        $this->webTestCase->assertNotNull($entity);
    }

    public function entitiesMatch(mixed $expectedEntity, mixed $entity): void
    {
        $this->webTestCase->assertEquals($expectedEntity, $entity);
    }

    public function flashMessage(string $expectedFlashMessage): void
    {
        $this->webTestCase->assertSelectorTextContains(".flash__body__message", $expectedFlashMessage);
    }

    public function pageNotFound(): void
    {
        $this->webTestCase->assertSelectorTextContains("h2.exception-http", "HTTP 404 Not Found");
    }

    public function pageAccessDenied(): void
    {
        $this->webTestCase->assertSelectorTextContains("h2.exception-http", "HTTP 403 Forbidden");
    }

    public function pageMethodNotAllowed(): void
    {
        $this->webTestCase->assertSelectorTextContains("h2.exception-http", "HTTP 405 Method Not Allowed");
    }
}
