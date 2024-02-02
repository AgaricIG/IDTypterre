<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UcsRepository")
 * @ORM\Table(name="ucs",indexes={@ORM\Index(name="ucs_id_idx", columns={"id"})})
 * @UniqueEntity(fields={"id"}, message="Cet ID existe déjà...")
 */
class Ucs
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tree = [];

    /**
     * @ORM\Column(name="geom", type="geometry", nullable=true, options={"geometry_type"="GEOMETRY"})
     */
    private $geom = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Uts", mappedBy="Ucs")
     */
    private $uts;

    public function __construct()
    {
        $this->uts = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): ?self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGeom()
    {
        return $this->geom;
    }

    public function setGeom($geom): self
    {
        $this->geom = $geom;

        return $this;
    }

    public function hasGeom(): bool
    {
        return isset($this->geom);
    }

    public function getTree(): ?array
    {
        return $this->tree;
    }

    public function setTree(?array $tree): self
    {
        $this->tree = $tree;

        return $this;
    }

    /**
     * @return Collection|Uts[]
     */
    public function getUts(): Collection
    {
        return $this->uts;
    }

    public function addUt(Uts $ut): self
    {
        if (!$this->uts->contains($ut)) {
            $this->uts[] = $ut;
            $ut->addUc($this);
        }

        return $this;
    }

    public function removeUt(Uts $ut): self
    {
        if ($this->uts->contains($ut)) {
            $this->uts->removeElement($ut);
            $ut->removeUc($this);
        }

        return $this;
    }
}
