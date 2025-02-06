<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Timestampable
{
    #[ORM\Column(type: "datetime")]
    #[Gedmo\Timestampable(on: "create")]
    private DateTime $created;

    #[ORM\Column(type: "datetime")]
    #[Gedmo\Timestampable(on: "update")]
    private DateTime $updated;

    public function setCreated(Datetime $created): void
    {
        $this->created = $created;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setUpdated(Datetime $updated): void
    {
        $this->updated = $updated;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }
}
