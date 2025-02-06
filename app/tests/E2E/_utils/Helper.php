<?php

namespace Tests\E2E\_utils;

use DateTime;
use Symfony\Component\Panther\Client;

class Helper
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @return array<string>
     */
    public function getCookiesName(): array
    {
        $cookies = $this->client->getCookieJar()->all();
        return array_map(fn($cookie) => $cookie->getName(), $cookies);
    }

    public function getUniqueEmail(): string
    {
        return "test" . uniqid(strval(mt_rand()), true) . "@test.com";
    }

    public static function getDateInTheFuture(int $days = null): DateTime
    {
        $randomDays = $days ?? random_int(1, 365);
        return new DateTime("+{$randomDays} days");
    }

    public static function formatDateForHtmlInput(DateTime $dateTime): string
    {
        return $dateTime->format("m-d-Y\TH:i");
    }

    public static function formatDateForSql(DateTime $dateTime): DateTime
    {
        return new DateTime($dateTime->format("Y-m-d H:i:00"));
    }

    /**
     * @param array<string, string> $parameters
     */
    public function buildPath(string $url, array $parameters): string
    {
        foreach ($parameters as $key => $value) {
            $placeholder = "{" . $key . "}";
            if (strpos($url, $placeholder) !== false) {
                $url = str_replace($placeholder, $value, $url);
            }
        }

        return $url;
    }
}
