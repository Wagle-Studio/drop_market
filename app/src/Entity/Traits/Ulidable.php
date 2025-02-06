<?php

namespace App\Entity\Traits;

use Symfony\Component\Uid\Ulid;
use Doctrine\ORM\Mapping as ORM;

trait Ulidable
{
    #[ORM\Column(type: 'ulid', unique: true)]
    private Ulid $ulid;

    public function initializeUlid(): void
    {
        $this->ulid = new Ulid();
    }

    public function getUlid(): string
    {
        return $this->ulid->__toString();
    }
}
