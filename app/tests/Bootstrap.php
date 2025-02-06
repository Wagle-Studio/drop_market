<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . "/vendor/autoload.php";

// Uses custom configuration file exists in config/bootstrap.php if it exists.
if (file_exists(dirname(__DIR__) . "/config/bootstrap.php")) {
    require dirname(__DIR__) . "/config/bootstrap.php";
} else {
    // Otherwise, we use Dotenv to load the environment.
    if (file_exists(dirname(__DIR__) . "/.env.test.local")) {
        (new Dotenv())->bootEnv(dirname(__DIR__) . "/.env.test.local");
    } elseif (file_exists(dirname(__DIR__) . "/.env.test")) {
        (new Dotenv())->bootEnv(dirname(__DIR__) . "/.env.test");
    }
}
