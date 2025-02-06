<?php

namespace Tests\E2E;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;
use Tests\E2E\_utils\ActionHelper;
use Tests\E2E\_utils\AppRouteTrait;
use Tests\E2E\_utils\DebugHelper;
use Tests\E2E\_utils\Helper;
use Tests\E2E\_utils\RepositoryHelper;
use Tests\E2E\_utils\VerifyHelper;

class AbstractE2ETest extends PantherTestCase
{
    use AppRouteTrait;

    protected Client $client;
    protected ContainerInterface $container;
    protected Helper $helper;
    protected DebugHelper $debug;
    protected ActionHelper $action;
    protected VerifyHelper $verify;
    protected RepositoryHelper $repository;

    protected function setup(): void
    {
        parent::setUp();
        $this->client = static::createPantherClient();
        $this->container = self::getContainer();

        $this->helper = new Helper($this->client);
        $this->debug = new DebugHelper($this->client);
        $this->action = new ActionHelper($this->client);
        $this->verify = new VerifyHelper($this->client, $this);
        $this->repository = new RepositoryHelper($this->container);
    }

    protected function getService(string $serviceId): object
    {
        return $this->container->get($serviceId);
    }

    protected function clearService(string $serviceId): void
    {
        $this->container->get($serviceId)->getManager()->clear();
    }
}
