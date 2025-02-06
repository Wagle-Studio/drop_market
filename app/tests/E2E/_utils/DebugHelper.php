<?php

namespace Tests\E2E\_utils;

use Symfony\Component\Panther\Client;

class DebugHelper
{
    use AppRouteTrait;

    public function __construct(private Client $client)
    {
    }

    public function takeScreenshot(string $filename): void
    {
        $directory = __DIR__ . "/_screenshots/";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $this->client->takeScreenshot($directory . "/" . $filename . ".png");
    }

    public function dumpHtml(string $filename): void
    {
        $directory = __DIR__ . "/_html_dumps/";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($directory . "/" . $filename . ".html", $this->client->getPageSource());
    }
}
