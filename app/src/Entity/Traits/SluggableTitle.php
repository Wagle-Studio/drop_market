<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait SluggableTitle
{
    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Gedmo\Slug(fields: ["title"])]
    private string $slug;

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
